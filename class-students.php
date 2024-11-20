<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

if (!isset($_SESSION['alogin']) || $_SESSION['alogin'] == '') {
    header("Location: index.php");
    exit();
} else {
    // Check if classid is set in the URL
    $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;

    // Fetch class details and students
    $sql = "SELECT ClassName, Section FROM tblclasses WHERE id = :classid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':classid', $classid, PDO::PARAM_INT);
    $query->execute();
    $classDetails = $query->fetch(PDO::FETCH_OBJ);

    $sql = "SELECT StudentName, RollId, StudentId FROM tblstudents WHERE ClassName = :classid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':classid', $classid, PDO::PARAM_INT);
    $query->execute();
    $students = $query->fetchAll(PDO::FETCH_OBJ);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo htmlentities($classDetails->ClassName . " - " . $classDetails->Section); ?> | Students</title>
        <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
        <link rel="stylesheet" href="css/main.css" media="screen">
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
                                <div class="col-sm-6">
                                    <h2 class="title">Students in <?php echo htmlentities($classDetails->ClassName . " - " . $classDetails->Section); ?></h2>
                                </div>
                            </div>
                            <div class="row">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Roll ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($query->rowCount() > 0) {
                                            $cnt = 1;
                                            foreach ($students as $student) {
                                        ?>
                                                <tr>
                                                    <td><?php echo htmlentities($cnt); ?></td>
                                                    <td><?php echo htmlentities($student->StudentId); ?></td>
                                                    <td><?php echo htmlentities($student->StudentName); ?></td>
                                                    <td><?php echo htmlentities($student->RollId); ?></td>
                                                </tr>
                                        <?php
                                                $cnt++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='4'>No students found for this class.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
    </body>

    </html>
<?php } ?>
