<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include('includes/config.php');

// Check if the user is logged in
if (strlen($_SESSION['alogin']) == 0) {
    header("Location: index.php"); // Redirect to login if not logged in
} else {
    // Check if the Student ID is provided
    if (isset($_GET['stid'])) {
        $studentId = intval($_GET['stid']);

        // Prepare the DELETE SQL statement
        $stmt = $dbh->prepare("DELETE FROM tblstudents WHERE StudentId = :id");
        $stmt->bindParam(':id', $studentId, PDO::PARAM_INT);

        // Execute the statement and check if the deletion was successful
        if ($stmt->execute()) {
            $_SESSION['msg'] = "Student record deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete the student record.";
        }
    } else {
        $_SESSION['error'] = "Invalid Student ID.";
    }

    // Redirect back to the Manage Students page
    header("Location: manage-students.php");
    exit();
}
?>
