<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize messages
$msg = "";
$error = "";

// Include database configuration
include('includes/config.php');

// Check if the user is logged in
if (strlen($_SESSION['alogin']) == 0) {
    header("Location: index.php");
    exit();
}

// Get the student ID from the URL
$studentId = isset($_GET['stid']) ? intval($_GET['stid']) : 0; // Sanitize input
$studentName = ""; // Initialize the student name variable

// Fetch student data for the given student ID
if ($studentId) {
    $stmt = $dbh->prepare("SELECT StudentName FROM tblstudents WHERE StudentId = :student_id");
    $stmt->bindParam(':student_id', $studentId);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_OBJ);

    if ($studentData) {
        $studentName = $studentData->StudentName; // Assign the student name
    } else {
        $error = "Student not found.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from POST
    $month = $_POST['month'];
    $year = $_POST['year'];
    $percentage = $_POST['percentage']; // Single percentage for the month


    // Prepare the SQL statement to insert the report card
    for ($week = 1; $week <= 4; $week++) {
        $attendance = $_POST['attendance'][$week - 1];
        $interaction_rating = $_POST['interaction_rating'][$week - 1];
        $test_rating = $_POST['test_rating'][$week - 1];
        $assignment_rating = $_POST['assignment_rating'][$week - 1];
        $overall_performance = $_POST['overall_performance'][$week - 1];

        // Prepare the SQL statement for each week
        $stmt = $dbh->prepare("INSERT INTO report_cards (StudentId, Month, Year, Week, Attendance, InteractionRating, TestRating, AssignmentRating, OverallPerformance, percentage) 
                                VALUES (:student_id, :month, :year, :week, :attendance, :interaction_rating, :test_rating, :assignment_rating, :overall_performance, :percentage)");

        // Bind the parameters
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':week', $week);
        $stmt->bindParam(':attendance', $attendance);
        $stmt->bindParam(':interaction_rating', $interaction_rating);
        $stmt->bindParam(':test_rating', $test_rating);
        $stmt->bindParam(':assignment_rating', $assignment_rating);
        $stmt->bindParam(':overall_performance', $overall_performance);
        $stmt->bindParam(':percentage', $percentage);

        // Execute the statement
        $stmt->execute();
    }

    // Set a success message and redirect
    $_SESSION['msg'] = "Report card added successfully.";
    header("Location: manage-students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Report Card</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/jquery.min.js"></script>
    <style>
        .panel {
            max-width: 900px;
            /* Adjust this value as needed */
            margin: 0 auto;
            /* Center the panel */
            padding: 20px;
        }
    </style>
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
                                                <label for="student_name" class="col-sm-2 control-label">Student Name</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="student_name" class="form-control" value="<?php echo htmlentities($studentName); ?>" readonly>
                                                    <input type="hidden" name="student_id" value="<?php echo htmlentities($studentId); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="month" class="col-sm-2 control-label">Month</label>
                                                <div class="col-sm-10">
                                                    <select name="month" class="form-control" required="required">
                                                        <option value="">Select Month</option>
                                                        <?php
                                                        $months = [
                                                            'January',
                                                            'February',
                                                            'March',
                                                            'April',
                                                            'May',
                                                            'June',
                                                            'July',
                                                            'August',
                                                            'September',
                                                            'October',
                                                            'November',
                                                            'December'
                                                        ];
                                                        foreach ($months as $month) {
                                                            echo "<option value='$month'>$month</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="year" class="col-sm-2 control-label">Year</label>
                                                <div class="col-sm-10">
                                                    <input type="number" name="year" class="form-control" required="required" placeholder="Enter Year">
                                                </div>
                                            </div>

                                            <!-- Weekly ratings -->
                                            <div class="form-group">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Week</th>
                                                            <th>Attendance </th>
                                                            <th>Interaction </th>
                                                            <th>Test Rating </th>
                                                            <th>Assignment Rating </th>
                                                            <th>Overall Performance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php for ($week = 1; $week <= 4; $week++) { ?>
                                                            <tr>
                                                                <td>Week <?php echo $week; ?></td>
                                                                <td><input type="text" name="attendance[]" class="form-control" required="required"></td>
                                                                <td><input type="text" name="interaction_rating[]" class="form-control" required="required"></td>
                                                                <td><input type="text" name="test_rating[]" class="form-control" required="required"></td>
                                                                <td><input type="text" name="assignment_rating[]" class="form-control" required="required"></td>
                                                                <td><input type="text" name="overall_performance[]" class="form-control"></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Single percentage for the month -->
                                            <div class="form-group">
                                                <label for="percentage" class="col-sm-2 control-label">Overall Percentage for the Month</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="percentage" class="form-control" required="required" placeholder="Enter overall percentage for the month">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include('includes/footer.php'); ?>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentId = <?php echo json_encode($studentId); ?>;
        const monthSelect = document.querySelector('select[name="month"]');
        const yearInput = document.querySelector('input[name="year"]');

        function loadLocalStorageData() {
            const month = monthSelect.value;
            const year = yearInput.value;

            if (!month || !year || !studentId) return;

            const key = `reportFormData_${studentId}_${month}_${year}`;
            const savedData = JSON.parse(localStorage.getItem(key) || '{}');

            document.querySelectorAll('tbody tr').forEach((row, index) => {
                row.querySelector('input[name="attendance[]"]').value = savedData[`attendance[${index}]`] || '';
                row.querySelector('input[name="interaction_rating[]"]').value = savedData[`interaction_rating[${index}]`] || '';
                row.querySelector('input[name="test_rating[]"]').value = savedData[`test_rating[${index}]`] || '';
                row.querySelector('input[name="assignment_rating[]"]').value = savedData[`assignment_rating[${index}]`] || '';
                row.querySelector('input[name="overall_performance[]"]').value = savedData[`overall_performance[${index}]`] || '';
            });

            // Set the overall percentage for the month
            document.querySelector('input[name="percentage"]').value = savedData['percentage'] || '';
        }

        // Load data when the page loads or when month/year is changed
        monthSelect.addEventListener('change', loadLocalStorageData);
        yearInput.addEventListener('input', loadLocalStorageData);

        loadLocalStorageData(); // Initial load on page load
    });
</script>

</body>

</html>