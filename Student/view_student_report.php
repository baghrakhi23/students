<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include('includes/config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$studentId = $_SESSION['student_id'];
$reportCards = [];

// Handle form submission for selecting month and year
if (isset($_POST['view_report'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $rollId = $_POST['roll_id'];

    // Fetch report cards for the selected criteria
    $stmt = $dbh->prepare("SELECT * FROM report_cards WHERE StudentId = :student_id AND Month = :month AND Year = :year");
    $stmt->bindParam(':student_id', $studentId);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':year', $year);
    $stmt->execute();
    $reportCards = $stmt->fetchAll(PDO::FETCH_OBJ);
}

// Fetch student information
$studentStmt = $dbh->prepare("SELECT StudentName, RollId FROM tblstudents WHERE StudentId = :student_id");
$studentStmt->bindParam(':student_id', $studentId);
$studentStmt->execute();
$studentData = $studentStmt->fetch(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Student Report</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlentities($studentData->StudentName); ?> (Roll ID: <?php echo htmlentities($studentData->RollId); ?>)</h2>
        <form method="post">
            <div class="form-group">
                <label for="month">Select Month</label>
                <select name="month" class="form-control" required>
                    <option value="">Select Month</option>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <!-- Add all months here -->
                </select>
            </div>
            <div class="form-group">
                <label for="year">Select Year</label>
                <input type="number" name="year" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="roll_id">Roll ID</label>
                <input type="text" name="roll_id" class="form-control" required value="<?php echo htmlentities($studentData->RollId); ?>" readonly>
            </div>
            <button type="submit" name="view_report" class="btn btn-primary">View Report</button>
        </form>

        <?php if (!empty($reportCards)) { ?>
            <h3>Report Card for <?php echo htmlentities($month) . " " . htmlentities($year); ?></h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Week</th>
                        <th>Attendance</th>
                        <th>Interaction Rating</th>
                        <th>Test Rating</th>
                        <th>Assignment Rating</th>
                        <th>Overall Performance</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportCards as $report) { ?>
                        <tr>
                            <td><?php echo htmlentities($report->Week); ?></td>
                            <td><?php echo htmlentities($report->Attendance); ?></td>
                            <td><?php echo htmlentities($report->InteractionRating); ?></td>
                            <td><?php echo htmlentities($report->TestRating); ?></td>
                            <td><?php echo htmlentities($report->AssignmentRating); ?></td>
                            <td><?php echo htmlentities($report->OverallPerformance); ?></td>
                            <td><?php echo htmlentities($report->percentage); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } elseif (isset($_POST['view_report'])) { ?>
            <div class="alert alert-info">No report cards found for the selected month and year.</div>
        <?php } ?>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
