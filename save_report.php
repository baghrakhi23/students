<?php
session_start();
include('includes/config.php');

// Define month names array for easy lookup
$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May',
    6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = intval($_POST['student_id']);
    $month = intval($_POST['month']); // Use month as a number here
    $year = intval($_POST['year']);
    $week = intval($_POST['week']);
    $field = $_POST['field'];
    $value = $_POST['value'];

    // Update the report card
    $stmt = $dbh->prepare("UPDATE report_cards SET $field = :value WHERE StudentId = :student_id AND Month = :month AND Year = :year AND Week = :week");
    $stmt->bindParam(':value', $value);
    $stmt->bindParam(':student_id', $studentId);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':week', $week);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update report card']);
    }
}

// Check if month and year are set, then fetch data
if (isset($_POST['month']) && isset($_POST['year'])) {
    $selectedMonth = intval($_POST['month']); // Ensure month is an integer
    $selectedYear = intval($_POST['year']);
    
    // Fetch students
    $students = $dbh->query("SELECT StudentId, StudentName, RollId FROM tblstudents")->fetchAll(PDO::FETCH_OBJ);
    
    // Fetch report data with month number and year
    $reportDataQuery = $dbh->prepare("SELECT * FROM report_cards WHERE Month = :month AND Year = :year");
    $reportDataQuery->bindParam(':month', $selectedMonth); // Use month number here
    $reportDataQuery->bindParam(':year', $selectedYear);
    $reportDataQuery->execute();
    $reportData = $reportDataQuery->fetchAll(PDO::FETCH_OBJ);
}
?>
