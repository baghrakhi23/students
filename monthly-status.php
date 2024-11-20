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

// Initialize variables
$month = isset($_POST['month']) ? $_POST['month'] : '';
$year = isset($_POST['year']) ? $_POST['year'] : '';
$students = [];
$reportCards = [];

// Fetch all students from the database
$stmt = $dbh->prepare("SELECT StudentId, StudentName FROM tblstudents");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch report cards based on selected month and year
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $month && $year) {
    $stmt = $dbh->prepare("SELECT * FROM report_cards WHERE Month = :month AND Year = :year ORDER BY StudentId, Week");
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':year', $year);
    $stmt->execute();
    $reportCards = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle form submission to update the report_cards
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveReport'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];

    if (isset($_POST['studentId']) && is_array($_POST['studentId'])) {
        foreach ($_POST['studentId'] as $index => $studentId) {
            for ($week = 1; $week <= 4; $week++) {
                $testRating = $_POST['testRating'][$index][$week];
                $assignmentRating = $_POST['assignmentRating'][$index][$week];
                $attendance = $_POST['attendance'][$index][$week];
                $interactionRating = $_POST['interactionRating'][$index][$week];

                $stmt = $dbh->prepare("SELECT * FROM report_cards WHERE StudentId = :studentId AND Week = :week AND Month = :month AND Year = :year");
                $stmt->bindParam(':studentId', $studentId);
                $stmt->bindParam(':week', $week);
                $stmt->bindParam(':month', $month);
                $stmt->bindParam(':year', $year);
                $stmt->execute();
                $existingReport = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingReport) {
                    $stmt = $dbh->prepare("UPDATE report_cards SET TestRating = :testRating, AssignmentRating = :assignmentRating, Attendance = :attendance, InteractionRating = :interactionRating WHERE StudentId = :studentId AND Week = :week AND Month = :month AND Year = :year");
                } else {
                    $stmt = $dbh->prepare("INSERT INTO report_cards (StudentId, Week, Month, Year, TestRating, AssignmentRating, Attendance, InteractionRating) VALUES (:studentId, :week, :month, :year, :testRating, :assignmentRating, :attendance, :interactionRating)");
                }

                $stmt->bindParam(':studentId', $studentId);
                $stmt->bindParam(':week', $week);
                $stmt->bindParam(':month', $month);
                $stmt->bindParam(':year', $year);
                $stmt->bindParam(':testRating', $testRating);
                $stmt->bindParam(':assignmentRating', $assignmentRating);
                $stmt->bindParam(':attendance', $attendance);
                $stmt->bindParam(':interactionRating', $interactionRating);

                if (!$stmt->execute()) {
                    print_r($stmt->errorInfo()); // Debug output
                }
            }
        }
    } else {
        echo "No students were selected.";
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Manage Students</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .form-control.large-input {
            width: 100px;
            height: 40px;
            font-size: 12px;
        }
    </style>
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
                                <h2 class="title">Monthly Status Report</h2>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <form method="post" class="form-inline mb-4">
                                            <div class="form-group">
                                                <label for="month">Month</label>
                                                <select name="month" class="form-control" required>
                                                    <option value="">Select Month</option>
                                                    <?php 
                                                    $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                                    foreach ($months as $m) {
                                                        echo "<option value='$m' " . ($month == $m ? "selected" : "") . ">$m</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group mx-sm-3">
                                                <label for="year">Year</label>
                                                <input type="number" name="year" class="form-control" required placeholder="Enter Year" value="<?php echo $year; ?>">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Select</button>
                                        </form>

                                        <form id="reportForm">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Student Name</th>
                                                            <th colspan="4" class="text-center">Test Marks</th>
                                                            <th colspan="4" class="text-center">Assignment Marks</th>
                                                            <th colspan="4" class="text-center">Attendance</th>
                                                            <th colspan="4" class="text-center">Interaction Rating</th>
                                                        </tr>
                                                        <tr>
                                                            <th></th>
                                                            <?php for ($week = 1; $week <= 4; $week++): ?>
                                                                <th>Week <?= $week ?></th>
                                                            <?php endfor; ?>
                                                            <?php for ($week = 1; $week <= 4; $week++): ?>
                                                                <th>Week <?= $week ?></th>
                                                            <?php endfor; ?>
                                                            <?php for ($week = 1; $week <= 4; $week++): ?>
                                                                <th>Week <?= $week ?></th>
                                                            <?php endfor; ?>
                                                            <?php for ($week = 1; $week <= 4; $week++): ?>
                                                                <th>Week <?= $week ?></th>
                                                            <?php endfor; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($students as $index => $student): ?>
                                                            <tr>
                                                                <td><?= htmlentities($student['StudentName']) ?></td>
                                                                <input type="hidden" name="studentId[<?= $index ?>]" value="<?= $student['StudentId'] ?>">

                                                                <?php for ($week = 1; $week <= 4; $week++): ?>
                                                                    <td><input type="text" name="testRating[<?= $index ?>][<?= $week ?>]" class="form-control large-input" placeholder=""></td>
                                                                <?php endfor; ?>
                                                                <?php for ($week = 1; $week <= 4; $week++): ?>
                                                                    <td><input type="text" name="assignmentRating[<?= $index ?>][<?= $week ?>]" class="form-control large-input" placeholder=""></td>
                                                                <?php endfor; ?>
                                                                <?php for ($week = 1; $week <= 4; $week++): ?>
                                                                    <td><input type="text" name="attendance[<?= $index ?>][<?= $week ?>]" class="form-control large-input" placeholder=""></td>
                                                                <?php endfor; ?>
                                                                <?php for ($week = 1; $week <= 4; $week++): ?>
                                                                    <td><input type="text" name="interactionRating[<?= $index ?>][<?= $week ?>]" class="form-control large-input" placeholder=""></td>
                                                                <?php endfor; ?>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            loadFormData();

            $('#reportForm input').on('input', function() {
                saveFormData();
            });

            $('select[name="month"], input[name="year"]').on('change', function() {
                saveFormData();
            });

            function saveFormData() {
                const formData = $('#reportForm').serializeArray();
                localStorage.setItem('formData', JSON.stringify(formData));
            }

            function loadFormData() {
                const formData = JSON.parse(localStorage.getItem('formData'));
                if (formData) {
                    formData.forEach(item => {
                        $(`[name="${item.name}"]`).val(item.value);
                    });
                }
            }
        });
    </script>
</body>
</html>
