<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
} else {
    if (isset($_POST['submit'])) {
        $class = $_POST['class'];
        $studentid = $_POST['studentid'];
        $month = $_POST['month'];
        
        // Weekly ratings
        $interaction_rating_week1 = $_POST['interaction_rating_week1'];
        $test_rating_week1 = $_POST['test_rating_week1'];
        $assignment_rating_week1 = $_POST['assignment_rating_week1'];
        $overall_performance_week1 = $_POST['overall_performance_week1'];

        $interaction_rating_week2 = $_POST['interaction_rating_week2'];
        $test_rating_week2 = $_POST['test_rating_week2'];
        $assignment_rating_week2 = $_POST['assignment_rating_week2'];
        $overall_performance_week2 = $_POST['overall_performance_week2'];

        $interaction_rating_week3 = $_POST['interaction_rating_week3'];
        $test_rating_week3 = $_POST['test_rating_week3'];
        $assignment_rating_week3 = $_POST['assignment_rating_week3'];
        $overall_performance_week3 = $_POST['overall_performance_week3'];

        $interaction_rating_week4 = $_POST['interaction_rating_week4'];
        $test_rating_week4 = $_POST['test_rating_week4'];
        $assignment_rating_week4 = $_POST['assignment_rating_week4'];
        $overall_performance_week4 = $_POST['overall_performance_week4'];
        
        $percentage = $_POST['percentage'];

        $sql = "INSERT INTO report_cards (StudentId, month, interaction_rating_week1, test_rating_week1, assignment_rating_week1, overall_performance_week1,
                interaction_rating_week2, test_rating_week2, assignment_rating_week2, overall_performance_week2,
                interaction_rating_week3, test_rating_week3, assignment_rating_week3, overall_performance_week3,
                interaction_rating_week4, test_rating_week4, assignment_rating_week4, overall_performance_week4, percentage) 
                VALUES (:studentid, :month, :interaction_rating_week1, :test_rating_week1, :assignment_rating_week1, :overall_performance_week1,
                :interaction_rating_week2, :test_rating_week2, :assignment_rating_week2, :overall_performance_week2,
                :interaction_rating_week3, :test_rating_week3, :assignment_rating_week3, :overall_performance_week3,
                :interaction_rating_week4, :test_rating_week4, :assignment_rating_week4, :overall_performance_week4, :percentage)";
                
        $query = $dbh->prepare($sql);
        $query->bindParam(':studentid', $studentid, PDO::PARAM_STR);
        $query->bindParam(':month', $month, PDO::PARAM_STR);
        
        $query->bindParam(':interaction_rating_week1', $interaction_rating_week1, PDO::PARAM_STR);
        $query->bindParam(':test_rating_week1', $test_rating_week1, PDO::PARAM_STR);
        $query->bindParam(':assignment_rating_week1', $assignment_rating_week1, PDO::PARAM_STR);
        $query->bindParam(':overall_performance_week1', $overall_performance_week1, PDO::PARAM_STR);

        $query->bindParam(':interaction_rating_week2', $interaction_rating_week2, PDO::PARAM_STR);
        $query->bindParam(':test_rating_week2', $test_rating_week2, PDO::PARAM_STR);
        $query->bindParam(':assignment_rating_week2', $assignment_rating_week2, PDO::PARAM_STR);
        $query->bindParam(':overall_performance_week2', $overall_performance_week2, PDO::PARAM_STR);

        $query->bindParam(':interaction_rating_week3', $interaction_rating_week3, PDO::PARAM_STR);
        $query->bindParam(':test_rating_week3', $test_rating_week3, PDO::PARAM_STR);
        $query->bindParam(':assignment_rating_week3', $assignment_rating_week3, PDO::PARAM_STR);
        $query->bindParam(':overall_performance_week3', $overall_performance_week3, PDO::PARAM_STR);

        $query->bindParam(':interaction_rating_week4', $interaction_rating_week4, PDO::PARAM_STR);
        $query->bindParam(':test_rating_week4', $test_rating_week4, PDO::PARAM_STR);
        $query->bindParam(':assignment_rating_week4', $assignment_rating_week4, PDO::PARAM_STR);
        $query->bindParam(':overall_performance_week4', $overall_performance_week4, PDO::PARAM_STR);

        $query->bindParam(':percentage', $percentage, PDO::PARAM_STR);

        $query->execute();

        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            $msg = "Report card details added successfully";
        } else {
            $error = "Something went wrong. Please try again";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMS Admin | Add Report Card</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/select2/select2.min.css">
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/modernizr/modernizr.min.js"></script>
    <script>
        function getStudent(val) {
            $.ajax({
                type: "POST",
                url: "get_student.php",
                data: 'classid=' + val,
                success: function(data) {
                    $("#studentid").html(data);
                }
            });
        }
    </script>
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
                                <h2 class="title">Declare Report Card</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li class="active">Student Report Card</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <?php if ($msg) { ?>
                                            <div class="alert alert-success left-icon-alert" role="alert">
                                                <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                            </div><?php } else if ($error) { ?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            </div>
                                        <?php } ?>
                                        <form class="form-horizontal" method="post">
                                            <div class="form-group">
                                                <label for="default" class="col-sm-2 control-label">Class</label>
                                                <div class="col-sm-10">
                                                    <select name="class" class="form-control clid" id="classid" onChange="getStudent(this.value);" required="required">
                                                        <option value="">Select Course</option>
                                                        <?php $sql = "SELECT * from tblclasses";
                                                        $query = $dbh->prepare($sql);
                                                        $query->execute();
                                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                        if ($query->rowCount() > 0) {
                                                            foreach ($results as $result) { ?>
                                                                <option value="<?php echo htmlentities($result->id); ?>"><?php echo htmlentities($result->ClassName); ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="date" class="col-sm-2 control-label">Student Name</label>
                                                <div class="col-sm-10">
                                                    <select name="studentid" class="form-control stid" id="studentid" required="required">
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Report Card Fields -->
                                            <div class="form-group">
    <label for="month" class="col-sm-2 control-label">Month</label>
    <div class="col-sm-10">
        <select name="month" class="form-control" required="required">
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
            <option value="December">December</option>
        </select>
    </div>
</div>

<!-- Table for weekly ratings -->
<div class="form-group">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Week</th>
                <th>Interaction Rating</th>
                <th>Test Rating</th>
                <th>Assignment Rating</th>
                <th>Overall Performance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><input type="text" name="interaction_rating_week1" class="form-control" required="required"></td>
                <td><input type="text" name="test_rating_week1" class="form-control" required="required"></td>
                <td><input type="text" name="assignment_rating_week1" class="form-control" required="required"></td>
                <td><input type="text" name="overall_performance_week1" class="form-control" required="required"></td>
            </tr>
            <tr>
                <td>2</td>
                <td><input type="text" name="interaction_rating_week2" class="form-control" required="required"></td>
                <td><input type="text" name="test_rating_week2" class="form-control" required="required"></td>
                <td><input type="text" name="assignment_rating_week2" class="form-control" required="required"></td>
                <td><input type="text" name="overall_performance_week2" class="form-control" required="required"></td>
            </tr>
            <tr>
                <td>3</td>
                <td><input type="text" name="interaction_rating_week3" class="form-control" required="required"></td>
                <td><input type="text" name="test_rating_week3" class="form-control" required="required"></td>
                <td><input type="text" name="assignment_rating_week3" class="form-control" required="required"></td>
                <td><input type="text" name="overall_performance_week3" class="form-control" required="required"></td>
            </tr>
            <tr>
                <td>4</td>
                <td><input type="text" name="interaction_rating_week4" class="form-control" required="required"></td>
                <td><input type="text" name="test_rating_week4" class="form-control" required="required"></td>
                <td><input type="text" name="assignment_rating_week4" class="form-control" required="required"></td>
                <td><input type="text" name="overall_performance_week4" class="form-control" required="required"></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Percentage Ratings -->
<div class="form-group">
    <label for="percentage" class="col-sm-2 control-label">Percentage</label>
    <div class="col-sm-10">
        <input type="text" name="percentage" class="form-control" required="required">
    </div>
</div>

<!-- Submit Button -->
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" name="submit" class="btn btn-success">Add Report Card</button>
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
        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
        <script src="js/pace/pace.min.js"></script>
        <script src="js/lobipanel/lobipanel.min.js"></script>
        <script src="js/select2/select2.min.js"></script>
        <script src="js/main.js"></script>
        <script>
            $(function($) {
                $(".js-states").select2();
                $(".js-states-limit").select2({
                    maximumSelectionLength: 2
                });
                $(".js-states-hide").select2({
                    minimumResultsForSearch: Infinity
                });
            });
        </script>
    </body>

</html>
<?php } ?>
