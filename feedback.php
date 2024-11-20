<?php
session_start();
include('includes/config.php');

if (isset($_POST['submit_feedback'])) {
    $studentId = $_POST['studentid'];
    $feedback = $_POST['feedback'];

    $query = "INSERT INTO feedback (StudentId, Feedback) VALUES (:studentid, :feedback)";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':studentid', $studentId, PDO::PARAM_STR);
    $stmt->bindParam(':feedback', $feedback, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $message = "Thank you for your feedback!";
    } else {
        $message = "An error occurred. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container" style="max-width: 600px; margin-top: 50px;">
    <h2>Submit Feedback</h2>
    
    <?php if (isset($message)) { ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php } ?>
    
    <form method="post" action="feedback.php">
        <div class="form-group">
            <label for="feedback">Your Feedback:</label>
            <textarea class="form-control" id="feedback" name="feedback" rows="4" required></textarea>
        </div>
        <input type="hidden" name="studentid" value="<?php echo htmlentities($_POST['studentid']); ?>">
        <button type="submit" name="submit_feedback" class="btn btn-primary">Submit</button>
    </form>
</div>
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
</body>
</html>
