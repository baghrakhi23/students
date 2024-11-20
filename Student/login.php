<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include('../includes/config.php');

// Handle the login form submission
if (isset($_POST['login'])) {
    $rollId = $_POST['roll_id'];

    // Check if the Roll ID exists in the database
    $stmt = $dbh->prepare("SELECT StudentId FROM tblstudents WHERE RollId = :roll_id");
    $stmt->bindParam(':roll_id', $rollId);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_OBJ);

    if ($student) {
        // Set session and redirect to report card page
        $_SESSION['student_id'] = $student->StudentId;
        header("Location: view_student_report.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid Roll ID. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h2 class="text-center">Student Login</h2>
                <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger">
                        <?php echo htmlentities($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>
                <form method="post">
                    <div class="form-group">
                        <label for="roll_id">Roll ID</label>
                        <input type="text" class="form-control" name="roll_id" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
