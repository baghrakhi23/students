<?php
session_start();
include('includes/config.php');

// Query to fetch feedback along with the student name
$query = "SELECT feedback.Feedback, feedback.CreatedAt, tblstudents.StudentName
          FROM feedback
          JOIN tblstudents ON feedback.StudentId = tblstudents.StudentId
          ORDER BY feedback.CreatedAt DESC";
$stmt = $dbh->prepare($query);
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Feedback List</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f6;
        }
        .container {
            margin-top: 30px;
            max-width: 800px;
        }
        .title {
            color: #5c5c5c;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        .feedback-item {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .feedback-item h4 {
            margin: 0;
            font-size: 1.2em;
            color: #337ab7;
        }
        .feedback-item p {
            margin: 5px 0;
            font-size: 0.9em;
            color: #666;
        }
        .feedback-item .timestamp {
            font-size: 0.8em;
            color: #999;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="title">Student Feedback List</h2>

    <?php if ($feedbacks) { ?>
        <?php foreach ($feedbacks as $feedback) { ?>
            <div class="feedback-item">
                <h4><?php echo htmlentities($feedback->StudentName); ?></h4>
                <p><?php echo htmlentities($feedback->Feedback); ?></p>
                <div class="timestamp">Submitted on: <?php echo htmlentities($feedback->CreatedAt); ?></div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="alert alert-info">No feedback available.</div>
    <?php } ?>
</div>

<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
</body>
</html>
