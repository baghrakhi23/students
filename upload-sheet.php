<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

$msg = "";
$error = "";

// Define upload directory
$uploadDir = 'uploads/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if file was uploaded without errors
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileName = $_FILES['file']['name'];
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileType = $_FILES['file']['type'];

        // Validate file type (optional)
        $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
        if (in_array($fileType, $allowedTypes)) {
            // Move the file to the uploads directory
            if (move_uploaded_file($fileTmpPath, $uploadDir . $fileName)) {
                $msg = "File uploaded successfully: " . htmlentities($fileName);
            } else {
                $error = "Error moving the uploaded file.";
            }
        } else {
            $error = "Invalid file type. Please upload an Excel file.";
        }
    } else {
        $error = "Error uploading the file. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Student Report</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>
<div class="container">
    <h2>Import Excel Sheet for Student Report</h2>

    <!-- Display success or error messages -->
    <?php if (!empty($msg)) { ?>
        <div class="alert alert-success" role="alert">
            <strong>Success!</strong> <?php echo htmlentities($msg); ?>
        </div>
    <?php } else if (!empty($error)) { ?>
        <div class="alert alert-danger" role="alert">
            <strong>Error!</strong> <?php echo htmlentities($error); ?>
        </div>
    <?php } ?>

    <!-- Upload form -->
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="file">Choose Excel file:</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xls, .xlsx" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>

    <!-- Display imported data -->
    <?php
    if (isset($fileName) && file_exists($uploadDir . $fileName)) {
        // Load the spreadsheet
        $spreadsheet = IOFactory::load($uploadDir . $fileName);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(); // Convert sheet data to array

        echo '<h3>Student Report</h3>';
        echo '<table class="table table-bordered">';
        
        foreach ($rows as $index => $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                // Add table headers for the first row
                if ($index == 0) {
                    echo '<th>' . htmlentities($cell) . '</th>';
                } else {
                    echo '<td>' . htmlentities($cell) . '</td>';
                }
            }
            echo '</tr>';
        }

        echo '</table>';
    }
    ?>
</div>

<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
</body>
</html>
