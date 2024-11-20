<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$msg = "";
$error = "";
include('includes/config.php');

if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {
    // Fetch student data from the database
    $sql = "SELECT StudentName FROM tblstudents ORDER BY StudentName";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Sheet</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap.css" media="screen">
        <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
        <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
        <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
        <link rel="stylesheet" href="css/prism/prism.css" media="screen"> <!-- USED FOR DEMO HELP - YOU CAN REMOVE IT -->
        <link rel="stylesheet" href="css/main.css" media="screen">
        <script src="js/modernizr/modernizr.min.js"></script>
    <style>
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .trainer-row {
            background-color: #ffeb99;
        }
        .sunday {
            background-color: #ffcccc;
        }
    </style>
</head>
<body>

<div class="top-navbar-fixed">
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
                                <h2 class="title">Create Attendance Sheet</h2>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>Attendance Sheet</h5>
                                            </div>
                                        </div>

                                        <!-- Display success or error messages -->
                                        <?php if (!empty($msg)) { ?>
                                            <div class="alert alert-success left-icon-alert" role="alert">
                                                <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                            </div>
                                        <?php } else if (!empty($error)) { ?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            </div>
                                        <?php } ?>

                                        <!-- Attendance table -->
                                        <div class="panel-body">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Students Name</th>
                                                        <th colspan="7">Dates (October 1-7)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Trainer Information -->
                                                    <tr class="trainer-row">
                                                        <td colspan="2">Trainer - Kanchan Kamat</td>
                                                        <td>P</td><td>P</td><td>P</td><td>P</td><td>No Class</td><td class="sunday">Sunday</td><td>P</td>
                                                    </tr>
                                                    <tr class="trainer-row">
                                                        <td colspan="2">Trainer Regular Rating</td>
                                                        <td>08:05</td><td>08:09</td><td>08:07</td><td>08:08</td><td>No Class</td><td class="sunday"></td><td>08:04</td>
                                                    </tr>
                                                    <tr class="trainer-row">
                                                        <td colspan="2">Camera (on/off)</td>
                                                        <td>ON</td><td>ON</td><td>ON</td><td>ON</td><td>No Class</td><td class="sunday"></td><td>ON</td>
                                                    </tr>
                                                    <tr class="trainer-row">
                                                        <td colspan="2">Students Assignment aligned</td>
                                                        <td>Yes</td><td>Yes</td><td>Yes</td><td>Yes</td><td>No Class</td><td class="sunday"></td><td>Yes</td>
                                                    </tr>
                                                    <tr class="trainer-row">
                                                        <td colspan="2">That day what issue</td>
                                                        <td>No Issue</td><td>No Issue</td><td>No Issue</td><td>No Issue</td><td>No Class</td><td class="sunday"></td><td>No Issue</td>
                                                    </tr>
                                                    <!-- Students Information -->
                                                    <?php
                                                    $count = 1;
                                                    if ($query->rowCount() > 0) {
                                                        foreach ($results as $result) {
                                                            ?>
                                                            <tr>
                                                                <td><?php echo htmlentities($count); ?></td>
                                                                <td><?php echo htmlentities($result->StudentName); ?></td>
                                                                <td>P</td><td>P</td><td>P</td><td>P</td><td>No Class</td><td class="sunday">Sunday</td><td>P</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">Student Regular Rating</td>
                                                                <td>A</td><td>A</td><td>A</td><td>A</td><td>No Class</td><td class="sunday"></td><td>A</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">Camera (on/off)</td>
                                                                <td>OFF</td><td>OFF</td><td>OFF</td><td>OFF</td><td>No Class</td><td class="sunday"></td><td>OFF</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">That day what issue</td>
                                                                <td>No Issue</td><td>No Issue</td><td>No Issue</td><td>No Issue</td><td>No Class</td><td class="sunday"></td><td>No Issue</td>
                                                            </tr>
                                                            <?php
                                                            $count++;
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
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

    <!-- JavaScript Files -->
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/jquery-ui/jquery-ui.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/pace/pace.min.js"></script>
    <script src="js/lobipanel/lobipanel.min.js"></script>
    <script src="js/iscroll/iscroll.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
<?php } ?>
