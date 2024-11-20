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
    // Check if form is submitted
    // Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $folderName = $_POST['folderName'];
    $monthYear = $_POST['month'];

    // Debugging: Check what values are received
    echo "Folder Name: " . htmlentities($folderName) . "<br>";
    echo "Month Year: " . htmlentities($monthYear) . "<br>";

    // Insert data into the attendance table
    try {
        $sql = "INSERT INTO attendance (folder_name, month_year) VALUES (:folder_name, :month_year)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':folder_name', $folderName, PDO::PARAM_STR);
        $query->bindParam(':month_year', $monthYear, PDO::PARAM_STR);
        $query->execute();

        $msg = "Attendance sheet created successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ASD Admin Create Attendance Sheet</title>
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

        table {
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
        }

        input[type="text"] {
            width: 100%;
            text-align: center;
            padding: 5px;
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
                                <h2 class="title">Create Attendance Sheet</h2>
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
                                                <h5>Create Attendance Sheet</h5>
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

                                        <!-- Form to select folder and month -->
                                        <div class="panel-body">
                                            <form method="POST" action="">
                                                <div class="form-group">
                                                    <label for="folderName">Folder (Class/Batch Name)</label>
                                                    <input type="text" class="form-control" id="folderName" name="folderName" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="month">Select Month</label>
                                                    <input type="month" class="form-control" id="month" name="month" required>
                                                </div>

                                                <button type="submit" class="btn btn-primary">Generate Sheet</button>
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

        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/jquery-ui/jquery-ui.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
        <script src="js/pace/pace.min.js"></script>
        <script src="js/lobipanel/lobipanel.min.js"></script>
        <script src="js/iscroll/iscroll.js"></script>
        <script src="js/prism/prism.js"></script>
        <script src="js/main.js"></script>
    </div>
</body>
</html>
<?php } ?>
