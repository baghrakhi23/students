<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include('includes/config.php');

// Check if the user is logged in
if (strlen($_SESSION['alogin']) == 0) {
    header("Location: index.php");
    exit();
}

// Handle the student selection and fetch report cards
$reportCards = [];
$studentId = null;
$studentName = "";
$studentRollId = "";

if (isset($_POST['student_id'])) {
    $studentId = intval($_POST['student_id']);
    // Fetch the student's name and Roll ID
    $studentStmt = $dbh->prepare("SELECT StudentName, RollId FROM tblstudents WHERE StudentId = :student_id");
    $studentStmt->bindParam(':student_id', $studentId);
    $studentStmt->execute();
    $studentData = $studentStmt->fetch(PDO::FETCH_OBJ);
    $studentName = $studentData->StudentName;
    $studentRollId = $studentData->RollId;

    // Fetch report cards for the selected student, grouped by month
    $stmt = $dbh->prepare("SELECT * FROM report_cards WHERE StudentId = :student_id ORDER BY Month, Week");
    $stmt->bindParam(':student_id', $studentId);
    $stmt->execute();
    $reportCards = $stmt->fetchAll(PDO::FETCH_OBJ);
}

// Handle report card edit
if (isset($_POST['edit_report'])) {
    $reportId = intval($_POST['report_id']);
    $interactionRating = $_POST['interaction_rating'];
    $testRating = $_POST['test_rating'];
    $assignmentRating = $_POST['assignment_rating'];
    $overallPerformance = $_POST['overall_performance'];
    $attendance = $_POST['attendance'];
    $percentage = $_POST['percentage'];

    // Update the report card in the database
    $updateStmt = $dbh->prepare("UPDATE report_cards SET InteractionRating = :interaction_rating, 
                                   TestRating = :test_rating, AssignmentRating = :assignment_rating, 
                                   OverallPerformance = :overall_performance, Attendance = :attendance, 
                                   percentage = :percentage WHERE id = :report_id");
    
    // Bind parameters
    $updateStmt->bindParam(':interaction_rating', $interactionRating);
    $updateStmt->bindParam(':test_rating', $testRating);
    $updateStmt->bindParam(':assignment_rating', $assignmentRating);
    $updateStmt->bindParam(':overall_performance', $overallPerformance);
    $updateStmt->bindParam(':attendance', $attendance);
    $updateStmt->bindParam(':percentage', $percentage);
    $updateStmt->bindParam(':report_id', $reportId);
    
    // Execute the update statement
    if ($updateStmt->execute()) {
        $_SESSION['msg'] = "Report card updated successfully.";
        header("Location: view-reports.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating report card.";
    }
}

// Fetch students with at least one report card
$students = $dbh->query("
    SELECT s.StudentId, s.StudentName, s.RollId 
    FROM tblstudents s 
    JOIN report_cards r ON s.StudentId = r.StudentId 
    GROUP BY s.StudentId
")->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Reports</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/main.css" media="screen">
</head>

<body class="top-navbar-fixed">
    <div class="main-wrapper">
        <?php include('includes/topbar.php'); ?>
        <div class="content-wrapper">
            <div class="content-container">
                <?php include('includes/leftbar.php'); ?>
                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">View Reports</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li><a href="students-report.php">All Students monthly report</a></li>
                                    
                                    <li class="active">View Report</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <?php if (isset($_SESSION['msg'])) { ?>
                                            <div class="alert alert-success left-icon-alert" role="alert">
                                                <strong>Well done!</strong> <?php echo htmlentities($_SESSION['msg']); unset($_SESSION['msg']); ?>
                                            </div>
                                        <?php } else if (isset($_SESSION['error'])) { ?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['error']); unset($_SESSION['error']); ?>
                                            </div>
                                        <?php } ?>

                                        <form class="form-horizontal" method="post">
                                            <!-- Select Student -->
                                            <div class="form-group">
<label for="student_id" class="col-sm-2 control-label">Student</label>
                                                <div class="col-sm-10">
                                                    <select name="student_id" class="form-control" required="required" onchange="this.form.submit()">
                                                        <option value="">Select Student</option>
                                                        <?php foreach ($students as $student) { ?>
                                                            <option value="<?php echo $student->StudentId; ?>" <?php echo ($studentId == $student->StudentId) ? 'selected' : ''; ?>>
                                                                <?php echo $student->StudentName . " (Roll ID: " . $student->RollId . ")"; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>

                                        <?php if (!empty($reportCards)) { 
                                            $currentMonth = ""; ?>
                                            <h3><?php echo $studentName . " (Roll ID: " . $studentRollId . ")"; ?></h3>
                                            <!-- Edit Button for all reports -->
    <form method="get" action="edit_report.php">
        <input type="hidden" name="student_id" value="<?php echo $studentId; ?>">
        <button type="submit" class="btn btn-primary">Edit Report Cards</button>
    </form>


                                            <div class="report-cards">
                                                <?php foreach ($reportCards as $report) {
                                                    // Display month heading if changed
                                                    if ($currentMonth != $report->Month) {
                                                        if ($currentMonth != "") {
                                                            echo "</tbody></table>"; // Close previous month's table
                                                        }
                                                        $currentMonth = $report->Month;
                                                        echo "<h4>Month: " . $currentMonth . "</h4>";
                                                        echo "<table class='table table-bordered'>
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
                                                                <tbody>";
                                                    } ?>
                                                    <tr>
                                                        <td><?php echo $report->Week; ?></td>
                                                        <td><?php echo $report->Attendance; ?></td>
                                                        <td><?php echo $report->InteractionRating; ?></td>
                                                        <td><?php echo $report->TestRating; ?></td>
                                                        <td><?php echo $report->AssignmentRating; ?></td>
                                                        <td><?php echo $report->OverallPerformance; ?></td>
                                                        <td><?php echo $report->percentage; ?></td>
                                                        <td>

                                                            <!-- Edit Report Modal -->
                                                            <div class="modal fade" id="editReportModal<?php echo $report->id; ?>" tabindex="-1" role="dialog" aria-labelledby="editReportLabel" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="editReportLabel">Edit Report Card - Week <?php echo $report->Week; ?></h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form method="post">
                                                                                <input type="hidden" name="report_id" value="<?php echo $report->id; ?>">
                                                                                <div class="form-group">
                                                                                    <label for="attendance">Attendance</label>
                                                                                    <input type="text" name="attendance" class="form-control" value="<?php echo $report->Attendance; ?>" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="interaction_rating">Interaction Rating</label>
                                                                                    <input type="text" name="interaction_rating" class="form-control" value="<?php echo $report->InteractionRating; ?>" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="test_rating">Test Rating</label>
                                                                                    <input type="text" name="test_rating" class="form-control" value="<?php echo $report->TestRating; ?>" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="assignment_rating">Assignment Rating</label>
                                                                                    <input type="text" name="assignment_rating" class="form-control" value="<?php echo $report->AssignmentRating; ?>" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="overall_performance">Overall Performance</label>
                                                                                    <input type="text" name="overall_performance" class="form-control" value="<?php echo $report->OverallPerformance; ?>" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="percentage">Percentage</label>
                                                                                    <input type="text" name="percentage" class="form-control" value="<?php echo $report->percentage; ?>" required>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                    <button type="submit" name="edit_report" class="btn btn-primary">Save Changes</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
