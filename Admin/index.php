<?php
$con = mysqli_connect("localhost", "root", "", "matesdb");
session_start();

if (isset($_SESSION['admin_email'])) {
    // User is logged in
    $adminemail = $_SESSION['admin_email'];
    $get_admin = "SELECT * FROM admin WHERE email='$adminemail'";
    $run_admin = mysqli_query($con, $get_admin);
    $row_admin = mysqli_fetch_array($run_admin);
    $admin_name = $row_admin['name'];
    $admin_email = $row_admin['email'];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home Page</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap-icons.css" rel="stylesheet">
    <script src="../js/all.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        }
        .sidebar {
            height: auto;
            background-color: #1EDD57FF;
            color: #fff;
        }
        .nav-item .nav-link {
            color: #fff;
        }
        .navbar {
            background-color: #1EDD57FF;
            color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .content {
            height: 100vh;
        }
        .nav-item .nav-link i {
            margin-left: 10px;
            color: #fff;
        }
        .col-5 img {
            border: 2px solid black;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 25px;
        }
        .nav-link {
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <span class="nav-link mt-2"><?php echo $admin_name; ?></span>
                </div>
            </div>
            <ul class="nav ml-auto">
                <?php
                echo '<li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="bi bi-power"></i>Logout</a>
                    </li>';
            } else {
                echo "<script>alert('You must log in first.')</script>";
                echo "<script>window.open('../login.php','_self')</script>";
            }
                ?>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 sidebar d-fixed">
                <ul class="nav flex-column p-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php"><i class="bi bi-house-door"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Manage team</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=""></a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9 content" style="height: 100vh; overflow-y: scroll;">
             
         
            </div>
        </div>
    </div>
</body>
</html>