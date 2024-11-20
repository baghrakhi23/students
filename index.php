<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" href="css/main.css" media="screen">
                            <link rel="icon" href="asd.png" type="image/png">

    <style>
        .main-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .panel {
            text-align: center;
        }
        .panel-body {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }
        .sub-title {
            font-weight: bold;
        }
         .btn-custom {
            color: #fff;
            background-color: #9EDF9C;
            border: none;
            padding: 8px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            
        }
        .btn-custom:hover {
            font-size: 16px;
            padding: 12px 20px;

        }
    </style>
    <script src="js/modernizr/modernizr.min.js"></script>
</head>
<body class="">
    <div class="main-wrapper">
        <div class="container">
            <h1 class="text-center">Student Result Management System</h1>
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <section class="section">
                        <div class="panel">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h4>For Students</h4>
                                </div>
                            </div>
                            <div class="panel-body p-40">
                                <div class="section-title">
                                    <p class="sub-title">Student Result Management System</p>
                                </div>
                                <form class="form-horizontal" method="post">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="control-label">Search your result</label>
                                        <div>
                                            <a href="find-result.php" class="btn-custom">Click here</a>
                                        </div>
                                    </div>
                                </form>
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
    <script src="js/main.js"></script>
</body>
</html>
