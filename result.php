<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

// Check if Roll ID and Class are provided in the POST request
if (isset($_POST['rollid']) && isset($_POST['class'])) {
    $rollid = $_POST['rollid'];
    $classid = $_POST['class'];
    
    // Query to fetch student details including StudentId
    $query = "SELECT tblstudents.StudentName, tblstudents.RollId, tblstudents.StudentId, tblclasses.ClassName 
              FROM tblstudents 
              JOIN tblclasses ON tblstudents.ClassName = tblclasses.id 
              WHERE tblstudents.RollId = :rollid AND tblstudents.ClassName = :classid";
    
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':rollid', $rollid, PDO::PARAM_STR);
    $stmt->bindParam(':classid', $classid, PDO::PARAM_STR);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($student) {
        // Fetch report cards using StudentId
        $query = "SELECT * FROM report_cards WHERE StudentId = :studentid ORDER BY Year, Month, Week";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':studentid', $student->StudentId, PDO::PARAM_STR);
        $stmt->execute();
        $reportCards = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
        $error = "No student found with the provided Roll ID and Class.";
    }
} else {
    $error = "Invalid Roll ID or Class.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Report Cards</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <style>
        body {
            background-color: #f4f7f6;
           font-family: Verdana, Geneva, Tahoma, sans-serif;
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
      
        .panel-heading {
            background-color: #337ab7 !important;
            color: #fff !important;
            padding: 10px 15px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .table {
            font-size: 0.9em;
            margin: 0 auto;
            max-width: 50%;
        }
        .table-bordered > thead > tr {
            background-color: #f1f1f1;
            color: #333;
            font-weight: bold;
        }
        .table-bordered > tbody > tr > td {
            padding: 8px 15px;
        }
        .percentage-display {
            padding: 10px;
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
            font-size: 1.4em;
            color: #3c763d;
            border-radius: 5px;
        }
        .panel-body p {
            margin-bottom: 8px;
            font-size: 1em;
        }
        h3 {
            color: #337ab7;
            font-weight: bold;
            font-size: 18px;
            margin-top: 20px;
            text-align: center;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .title {
                font-size: 1.5em;
            }
            .panel-body p, .percentage-display {
                font-size: 0.9em;
            }
            h3 {
                font-size: 2em;
            }
            .table {
                font-size: 0.85em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="title">Student Report Cards</h2>

    <?php if (isset($student)) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Student Details</h3>
            </div>
            <div class="panel-body">
                <p><b>Student Name:</b> <?php echo htmlentities($student->StudentName); ?></p>
                <p><b>Roll ID:</b> <?php echo htmlentities($student->RollId); ?></p>
                <p><b>Batch:</b> <?php echo htmlentities($student->ClassName); ?> </p>
            </div>
        </div>

        <?php 

        



if ($student) {
    // Query to fetch report cards and class name
    $query = "SELECT report_cards.*, tblclasses.ClassName 
              FROM report_cards 
              JOIN tblclasses ON report_cards.ClassId = tblclasses.id 
              WHERE report_cards.StudentId = :studentid 
              ORDER BY Year, Month, Week";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':studentid', $student->StudentId, PDO::PARAM_STR);
    $stmt->execute();
    $reportCards = $stmt->fetchAll(PDO::FETCH_OBJ);

    if ($reportCards) {
        $currentMonth = null;
        $currentYear = null;

        $monthlyPercentage = null; // Variable to store monthly percentage

        foreach ($reportCards as $report) {
            if ($currentMonth !== $report->Month || $currentYear !== $report->Year) {
                // Display previous month's percentage if set
                if ($currentMonth !== null && $currentYear !== null) {
                    echo "</tbody></table></div>";
                    echo "<div class='percentage-display'>Percentage: " . htmlentities($monthlyPercentage) . "</div><br>";
                }

                // Set new month and year
                $currentMonth = $report->Month;
                $currentYear = $report->Year;
                $monthlyPercentage = $report->percentage; // Set new monthly percentage

                // Start new month table with Class Name, Month, and Year
                echo "<h3>Report Cards for " . htmlentities($report->ClassName) . "  " . htmlentities($currentMonth) . " , " . htmlentities($currentYear) . "</h3>";
                echo '<div class="table-responsive"><table class="table table-bordered">';
                echo '<thead>
                        <tr>
                            <th>Week</th>
                            <th>InteractionRating</th>
                            <th>TestRating</th>
                            <th>AssignmentRating</th>
                            <th>Attendance</th>
                            <th>OverallPerformance</th>
                        </tr>
                      </thead>
                      <tbody>';
            }

            // Display the current row data
            echo "<tr>
                    <td>" . htmlentities($report->Week) . "</td>
                    <td>" . htmlentities($report->InteractionRating) . "</td>
                    <td>" . htmlentities($report->TestRating) . "</td>
                    <td>" . htmlentities($report->AssignmentRating) . "</td>
                    <td>" . htmlentities($report->Attendance) . "</td>
                    <td>" . htmlentities($report->OverallPerformance) . "</td>
                  </tr>";
        }

        // Display the last month's percentage
        if ($currentMonth !== null && $currentYear !== null) {
            echo '</tbody></table></div>';
            echo "<div class='percentage-display'>Percentage: " . htmlentities($monthlyPercentage) . "</div>";
        }
    } else { ?>
        <div class="alert alert-warning">No report cards available for this student.</div>
    <?php }
} else { ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php } ?>

    
</div>
<div style="text-align: center; margin-top: 20px;">
    <form action="feedback.php" method="post">
        <input type="hidden" name="studentid" value="<?php echo htmlentities($student->StudentId); ?>">
        <button type="submit" class="btn btn-primary">Submit Feedback</button>
    </form>
</div>

<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
</body>
</html>
<?php } ?>