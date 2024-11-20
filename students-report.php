<?php
session_start();
include('includes/config.php');

// Define the month names array at the top of the script
$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May',
    6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

// Set selected month and year if POST data is available
$selectedMonth = $_POST['month'] ?? null;
$selectedYear = $_POST['year'] ?? null;

// Fetch the report data as needed (if month and year are selected)
if ($selectedMonth && $selectedYear) {
    // Fetch students
    $students = $dbh->query("SELECT StudentId, StudentName, RollId FROM tblstudents")->fetchAll(PDO::FETCH_OBJ);

    // Fetch report data
    $reportDataQuery = $dbh->prepare("SELECT * FROM report_cards WHERE Month = :month AND Year = :year");
    $reportDataQuery->bindParam(':month', $selectedMonth, PDO::PARAM_INT);  // Use month number
    $reportDataQuery->bindParam(':year', $selectedYear, PDO::PARAM_INT);
    $reportDataQuery->execute();
    $reportData = $reportDataQuery->fetchAll(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Students Report</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Month and Year Selection Form -->
        <form method="post" class="form-inline">
            <label>Month:</label>
            <select name="month" class="form-control" required>
                <option value="">Select Month</option>
                <?php 
                foreach ($months as $m => $monthName) {
                    echo "<option value='$m' " . ($m == $selectedMonth ? 'selected' : '') . ">$monthName</option>";
                }
                ?>
            </select>

            <label>Year:</label>
            <select name="year" class="form-control" required>
                <option value="">Select Year</option>
                <?php for ($y = date("Y") - 5; $y <= date("Y") + 5; $y++) {
                    echo "<option value='$y' " . ($y == $selectedYear ? 'selected' : '') . ">$y</option>";
                } ?>
            </select>
            <button type="submit" class="btn btn-primary">Show Reports</button>
        </form>

        <!-- Report Display Table -->
        <?php if (!empty($students) && $selectedMonth && $selectedYear) { ?>
            <h3>Reports for <?php echo $months[$selectedMonth] . " " . $selectedYear; ?></h3>
            <form id="reportForm">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Roll ID</th>
                            <?php for ($week = 1; $week <= 4; $week++) { ?>
                                <th colspan="4">Week <?php echo $week; ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th></th><th></th>
                            <?php for ($week = 1; $week <= 4; $week++) { ?>
                                <th>Attendance</th><th>Interaction</th><th>Test</th><th>Assignment</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student) {
                            $studentReports = array_filter($reportData, fn($r) => $r->StudentId == $student->StudentId);
                            ?>
                            <tr>
                                <td><?php echo $student->StudentName; ?></td>
                                <td><?php echo $student->RollId; ?></td>
                                <?php for ($week = 1; $week <= 4; $week++) {
                                    $weekData = current(array_filter($studentReports, fn($r) => $r->Week == $week));
                                    ?>
                                    <td><input type="text" name="attendance" class="form-control report-input"
                                        data-student-id="<?php echo $student->StudentId; ?>"
                                        data-month="<?php echo $selectedMonth; ?>"
                                        data-year="<?php echo $selectedYear; ?>"
                                        data-week="<?php echo $week; ?>"
                                        data-field="Attendance"
                                        value="<?php echo $weekData->Attendance ?? ''; ?>"></td>
                                    <td><input type="text" name="interaction_rating" class="form-control report-input"
                                        data-student-id="<?php echo $student->StudentId; ?>"
                                        data-month="<?php echo $selectedMonth; ?>"
                                        data-year="<?php echo $selectedYear; ?>"
                                        data-week="<?php echo $week; ?>"
                                        data-field="InteractionRating"
                                        value="<?php echo $weekData->InteractionRating ?? ''; ?>"></td>
                                    <td><input type="text" name="test_rating" class="form-control report-input"
                                        data-student-id="<?php echo $student->StudentId; ?>"
                                        data-month="<?php echo $selectedMonth; ?>"
                                        data-year="<?php echo $selectedYear; ?>"
                                        data-week="<?php echo $week; ?>"
                                        data-field="TestRating"
                                        value="<?php echo $weekData->TestRating ?? ''; ?>"></td>
                                    <td><input type="text" name="assignment_rating" class="form-control report-input"
                                        data-student-id="<?php echo $student->StudentId; ?>"
                                        data-month="<?php echo $selectedMonth; ?>"
                                        data-year="<?php echo $selectedYear; ?>"
                                        data-week="<?php echo $week; ?>"
                                        data-field="AssignmentRating"
                                        value="<?php echo $weekData->AssignmentRating ?? ''; ?>"></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
        <?php } ?>
    </div>

    <script>
        $(document).ready(function() {
            $('.report-input').on('blur', function() {
                let input = $(this);
                let data = {
                    student_id: input.data('student-id'),
                    month: input.data('month'),
                    year: input.data('year'),
                    week: input.data('week'),
                    field: input.data('field'),
                    value: input.val()
                };

                $.ajax({
                    url: 'save_report.php',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        let res = JSON.parse(response);
                        if (res.status === 'success') {
                            console.log("Data saved successfully.");
                        } else {
                            console.error("Error saving data:", res.message);
                        }
                    },
                    error: function() {
                        console.error("Failed to save data.");
                    }
                });
            });
        });
    </script>
</body>
</html>
