<?php
session_start();

$msg = "";
$error = "";
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {

    $stid = intval($_GET['stid']);

    if (isset($_POST['submit'])) {
        $studentname = $_POST['fullname'];
        $rollid = $_POST['rollid'];
        $studentemail = $_POST['emailid'];
        $classid = $_POST['class'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];

        $sql = "UPDATE tblstudents SET StudentName=:studentname, RollId=:rollid, StudentEmail=:studentemail, ClassName=:classid, Gender=:gender, DOB=:dob WHERE StudentId=:stid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':studentname', $studentname, PDO::PARAM_STR);
        $query->bindParam(':rollid', $rollid, PDO::PARAM_STR);
        $query->bindParam(':studentemail', $studentemail, PDO::PARAM_STR);
        $query->bindParam(':classid', $classid, PDO::PARAM_INT);
        $query->bindParam(':gender', $gender, PDO::PARAM_STR);
        $query->bindParam(':dob', $dob, PDO::PARAM_STR);
        $query->bindParam(':stid', $stid, PDO::PARAM_INT);
        $query->execute();

        $msg = "Student info updated successfully";
    }

    $sql = "SELECT tblstudents.StudentName, tblstudents.RollId, tblstudents.StudentId, tblstudents.StudentEmail, tblstudents.Gender, tblstudents.DOB, tblclasses.ClassName, tblclasses.Section, tblclasses.id as ClassId 
            FROM tblstudents 
            JOIN tblclasses ON tblclasses.id = tblstudents.ClassName 
            WHERE tblstudents.StudentId = :stid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':stid', $stid, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    $classQuery = "SELECT id, ClassName, Section FROM tblclasses";
    $classResult = $dbh->query($classQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMS Admin | Edit Student</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" href="css/select2/select2.min.css">
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/modernizr/modernizr.min.js"></script>
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
                            <div class="col-md-6">
                                <h2 class="title">Edit Student Information</h2>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>Edit Student Info</h5>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <?php if ($msg) { ?>
                                                <div class="alert alert-success left-icon-alert" role="alert">
                                                    <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                                </div>
                                            <?php } else if ($error) { ?>
                                                <div class="alert alert-danger left-icon-alert" role="alert">
                                                    <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                                </div>
                                            <?php } ?>
                                            <form class="form-horizontal" method="post">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Full Name</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="fullname" class="form-control" value="<?php echo htmlentities($result->StudentName); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Roll No.</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="rollid" class="form-control" value="<?php echo htmlentities($result->RollId); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Email ID</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" name="emailid" class="form-control" value="<?php echo htmlentities($result->StudentEmail); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Class</label>
                                                    <div class="col-sm-10">
                                                        <select name="class" class="form-control" required>
                                                            <option value="<?php echo htmlentities($result->ClassId); ?>">
                                                                <?php echo htmlentities($result->ClassName . " (" . $result->Section . ")"); ?>
                                                            </option>
                                                            <?php while ($classRow = $classResult->fetch(PDO::FETCH_OBJ)) { ?>
                                                                <option value="<?php echo htmlentities($classRow->id); ?>">
                                                                    <?php echo htmlentities($classRow->ClassName . " (" . $classRow->Section . ")"); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Gender</label>
                                                    <div class="col-sm-10">
                                                        <select name="gender" class="form-control" required>
                                                            <option value="<?php echo htmlentities($result->Gender); ?>">
                                                                <?php echo htmlentities($result->Gender); ?>
                                                            </option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Date of Birth</label>
                                                    <div class="col-sm-10">
                                                        <input type="date" name="dob" class="form-control" value="<?php echo htmlentities($result->DOB); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-offset-2 col-sm-10">
                                                        <button type="submit" name="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/select2/select2.min.js"></script>
    <script>
        $(function() {
            $(".select2").select2();
        });
    </script>
</body>

</html>
<?php } ?>
