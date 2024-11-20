<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['student_id'])) {
    $studentId = intval($_GET['student_id']);
} else {
    header("Location: view-reports.php");
    exit();
}

$stmt = $dbh->prepare("SELECT * FROM report_cards WHERE StudentId = :student_id ORDER BY Month, Week");
$stmt->bindParam(':student_id', $studentId);
$stmt->execute();
$reportCards = $stmt->fetchAll(PDO::FETCH_OBJ);

if (isset($_POST['edit_report'])) {
    foreach ($_POST['report'] as $reportId => $reportData) {
        $interactionRating = $reportData['interaction_rating'];
        $testRating = $reportData['test_rating'];
        $assignmentRating = $reportData['assignment_rating'];
        $overallPerformance = $reportData['overall_performance'];
        $attendance = $reportData['attendance'];
        $percentage = $reportData['percentage'];

        $updateStmt = $dbh->prepare("UPDATE report_cards SET InteractionRating = :interaction_rating, 
                                       TestRating = :test_rating, AssignmentRating = :assignment_rating, 
                                       OverallPerformance = :overall_performance, Attendance = :attendance, 
                                       percentage = :percentage WHERE id = :report_id");

        $updateStmt->bindParam(':interaction_rating', $interactionRating);
        $updateStmt->bindParam(':test_rating', $testRating);
        $updateStmt->bindParam(':assignment_rating', $assignmentRating);
        $updateStmt->bindParam(':overall_performance', $overallPerformance);
        $updateStmt->bindParam(':attendance', $attendance);
        $updateStmt->bindParam(':percentage', $percentage);
        $updateStmt->bindParam(':report_id', $reportId);

        $updateStmt->execute();
    }
    $_SESSION['msg'] = "Report cards updated successfully.";
    header("Location: view-reports.php");
    exit();
}

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
    <title>Edit Reports</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
</head>
<body>
    <div class="container">
        <h2>Edit Reports for <?php echo $studentData->StudentName . " (Roll ID: " . $studentData->RollId . ")"; ?></h2>

        <form method="post">
            <?php
            $currentMonthYear = '';
            foreach ($reportCards as $report) {
                $monthYear = date("F Y", strtotime($report->Month));

                // Display month and year heading when it changes
                if ($monthYear !== $currentMonthYear) {
                    if ($currentMonthYear !== '') {
                        echo '</tbody></table>'; // Close the previous table body and table
                    }
                    echo '<h3>' . $monthYear . '</h3>';
                    echo '<table class="table table-bordered">
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
                            <tbody>';
                    $currentMonthYear = $monthYear;
                }
                ?>
                <tr>
                    <td><?php echo $report->Week; ?></td>
                    <td><input type="text" name="report[<?php echo $report->id; ?>][attendance]" value="<?php echo $report->Attendance; ?>" required></td>
                    <td><input type="text" name="report[<?php echo $report->id; ?>][interaction_rating]" value="<?php echo $report->InteractionRating; ?>" required></td>
                    <td><input type="text" name="report[<?php echo $report->id; ?>][test_rating]" value="<?php echo $report->TestRating; ?>" required></td>
                    <td><input type="text" name="report[<?php echo $report->id; ?>][assignment_rating]" value="<?php echo $report->AssignmentRating; ?>" required></td>
                    <td><input type="text" name="report[<?php echo $report->id; ?>][overall_performance]" value="<?php echo $report->OverallPerformance; ?>" required></td>
                    <td><input type="text" name="report[<?php echo $report->id; ?>][percentage]" value="<?php echo $report->percentage; ?>" required></td>
                </tr>
                <?php
            }
            echo '</tbody></table>'; // Close the last table
            ?>
            <button type="submit" name="edit_report" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
