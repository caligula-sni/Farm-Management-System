<?php

    session_start();
    require_once './include/connect/dbcon.php';

        try {
            $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // ... (previous code for user registration)
            if (isset($_POST["login"])) {
                if (empty($_POST["UserName"]) || empty ($_POST["PassWord"])) {
                    $message = '<label>All fields are required</label>';
                } else {
                    $pdoQuery = "SELECT * FROM tbuser WHERE UserName = :UserName";
                    $pdoResult = $pdoConnect->prepare($pdoQuery);
                    $pdoResult->execute(['UserName' => $_POST["UserName"]]);
                    $user = $pdoResult->fetch();

                    if ($user && password_verify($_POST["PassWord"], $user["PassWord"])) {

                        $_SESSION["UserName"] = $user["UserName"];
                        $_SESSION["role_id"] = $user["role_id"];
                        $_SESSION["id"] = $user["id"];
                        $_SESSION["cm_id"] = $user["cm_id"];
                        $_SESSION["province_id"] = $user['province_id'];

                        // Log the Log in action in audit trail with timestamp
                        $loggedInUser = $_SESSION["UserName"];
                        $pdoQuery = "INSERT INTO audit_trail (action, user) VALUES ('User logged in', :user)";
                        $pdoResult = $pdoConnect->prepare($pdoQuery);
                        $pdoResult->execute([':user' => $loggedInUser]);
                        
                        switch ($user["role_id"]) {
                            case 1:
                                header("location:./page/users/superadmin/home.php");
                                break;
                            case 2:
                                header("location:./page/users/admin/home.php");
                                break;
                            case 3:
                                header("location:./page/users/farmers/home.php");
                                break;
                            default:
                                $message = '<label>Wrong Data</label>';
                            }
                        exit;
                    } else {
                        $message = '<label>Wrong Data</label>';
                    }
                }
            }
        } catch (PDOException $error ) {
            $message = $error->getMessage();
        }
?>

        

        
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Crops Monitoring System</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="include/styles/img/favicon.png" rel="icon">
  <link href="include/styles/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="include/styles/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="include/styles/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="include/styles/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="include/styles/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="include/styles/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="include/styles/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="include/styles/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="include/styles/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo2 d-flex align-items-center w-auto">
                  <span class="d-none d-lg-block">Crops Monitoring System</span>
                </a>
              </div><!-- End Logo -->

              <?php
            if (isset($message)) {
                echo '<label>' . $message . '</label>'; 
            }
           ?>

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>

                  <form class="row g-3 needs-validation" method="POST" novalidate>

                    <div class="col-12">
                      <label for="UserName" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        
                        <input type="text" name="UserName" class="login-form" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="PassWord" class="form-label">Password</label>
                      <input type="password" name="PassWord" class="login-form" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>
                      <br> <br>
                    
                    <div class="col-12"> <br>
                      <button class="flexible-wide-button" name="login" value="login"type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Don't have account? <a href="register.php">Create an account</a></p>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">
                
                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="include/styles/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="include/styles/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="include/styles/vendor/chart.js/chart.umd.js"></script>
  <script src="include/styles/vendor/echarts/echarts.min.js"></script>
  <script src="include/styles/vendor/quill/quill.js"></script>
  <script src="include/styles/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="include/styles/vendor/tinymce/tinymce.min.js"></script>
  <script src="include/styles/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="include/styles/main.js"></script>

</body>

</html>