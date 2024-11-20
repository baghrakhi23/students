<?php
$msg = "";
$error = "";
include('includes/config.php');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Fetch class names from the database
$classOptions = [];
try {
    $stmt = $dbh->prepare("SELECT id, ClassName FROM tblclasses");
    $stmt->execute();
    $classOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error fetching class names: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['report_file'])) {
    $file = $_FILES['report_file']['tmp_name'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $classId = $_POST['class_id']; // Get the selected class ID

    // Load Excel file
    $spreadsheet = IOFactory::load($file);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    // Assuming the first row is the header
    $isHeader = true;
    foreach ($sheetData as $row) {
        if ($isHeader) {
            $isHeader = false; // Skip the header row
            continue;
        }

        $studentId = $row['A'];

        // Skip empty rows
        if (empty($studentId)) {
            continue;
        }

        // Extract values from the row
        $studentName = $row['B'];
        $testRatings = [$row['C'], $row['D'], $row['E'], $row['F']];
        $assignmentRatings = [$row['H'], $row['I'], $row['J'], $row['K']];
        $attendance = [$row['M'], $row['N'], $row['O'], $row['P']];
        $interactionRatings = [$row['R'], $row['S'], $row['T'], $row['U']];
        $overallPerformance = [$row['W'], $row['X'], $row['Y'], $row['Z']];
        $totalPercentage = !empty($row['AB']) ? $row['AB'] : 0;

        // Insert data into the database for each week
        for ($week = 1; $week <= 4; $week++) {
            $stmt = $dbh->prepare("INSERT INTO report_cards 
                (StudentId, ClassId, Month, Year, Week, TestRating, AssignmentRating, Attendance, InteractionRating, OverallPerformance, Percentage)
                VALUES 
                (:student_id, :class_id, :month, :year, :week, :test_rating, :assignment_rating, :attendance, :interaction_rating, :overall_performance, :percentage)");

            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':class_id', $classId);
            $stmt->bindParam(':month', $month);
            $stmt->bindParam(':year', $year);
            $stmt->bindParam(':week', $week);
            $stmt->bindParam(':test_rating', $testRatings[$week - 1]);
            $stmt->bindParam(':assignment_rating', $assignmentRatings[$week - 1]);
            $stmt->bindParam(':attendance', $attendance[$week - 1]);
            $stmt->bindParam(':interaction_rating', $interactionRatings[$week - 1]);
            $stmt->bindParam(':overall_performance', $overallPerformance[$week - 1]);
            $stmt->bindParam(':percentage', $totalPercentage);

            $stmt->execute();
        }
    }

    echo "Report card data imported successfully.";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMS Admin Create Class</title>
    <link rel="stylesheet" href="css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/modernizr/modernizr.min.js"></script>
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }
    </style>
</head>

<body class="top-navbar-fixed">
    <div class="main-wrapper">

        <!-- ========== TOP NAVBAR ========== -->
        <?php include('includes/topbar.php'); ?>

        <div class="content-wrapper">
            <div class="content-container">

                <!-- ========== LEFT SIDEBAR ========== -->
                <?php include('includes/leftbar.php'); ?>

                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Import Report Card</h2>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="container-fluid">

                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>Import Report Card</h5>
                                            </div>
                                        </div>
                                        <?php if ($msg) { ?>
                                            <div class="alert alert-success left-icon-alert" role="alert">
                                                <strong>Well done!</strong><?php echo htmlentities($msg); ?>
                                            </div><?php } else if ($error) { ?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            </div>
                                        <?php } ?>

                                        <div class="panel-body">
                                            <form action="" method="post" enctype="multipart/form-data">
                                                <div class="form-group has-success">
                                                    <label for="month" class="control-label">Select Month:</label>
                                                    <select name="month" id="month" class="form-control" required>
                                                        <option value="">Select Month</option>
                                                        <option value="January">January</option>
                                                        <option value="February">February</option>
                                                        <option value="March">March</option>
                                                        <option value="April">April</option>
                                                        <option value="May">May</option>
                                                        <option value="June">June</option>
                                                        <option value="July">July</option>
                                                        <option value="August">August</option>
                                                        <option value="September">September</option>
                                                        <option value="October">October</option>
                                                        <option value="November">November</option>
                                                        <!-- Add other months here -->
                                                        <option value="December">December</option>
                                                    </select>
                                                </div>

                                                <div class="form-group has-success">
                                                    <label for="year" class="control-label">Enter Year:</label>
                                                    <input type="number" name="year" id="year" class="form-control" required min="2000" max="<?php echo date("Y"); ?>" placeholder="e.g., 2024">
                                                </div>


                                                <div class="form-group has-success">
                                                    <label for="report_file" class="control-label">Emport Report Card Excel File:</label>
                                                    <input type="file" name="report_file" id="report_file" class="form-control" required>
                                                </div>

                                                <div class="form-group has-success">
                                                    <label for="class_id" class="control-label">Select Class:</label>
                                                    <select name="class_id" id="class_id" class="form-control" required>
                                                        <option value="">Select Class</option>
                                                        <?php foreach ($classOptions as $class) { ?>
                                                            <option value="<?php echo $class['id']; ?>">
                                                                <?php echo htmlentities($class['ClassName']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>


                                                <div class="form-group has-success">
                                                    <button type="submit" name="submit" class="btn btn-success btn-labeled">
                                                        Submit<span class="btn-label btn-label-right"><i class="fa fa-check"></i></span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/jquery-ui/jquery-ui.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/pace/pace.min.js"></script>
    <script src="js/lobipanel/lobipanel.min.js"></script>
    <script src="js/iscroll/iscroll.js"></script>
    <script src="js/prism/prism.js"></script>
    <script src="js/main.js"></script>
</body>

</html>