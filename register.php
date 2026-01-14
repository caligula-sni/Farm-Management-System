<?php

    session_start();
    require_once './include/connect/dbcon.php';

        try {
            $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Register new user
            if (isset($_POST["register"])) {
                $pdoConnect->beginTransaction();

                $UserName = $_POST['regUserName'];
                $PassWord = password_hash($_POST['regPassWord'], PASSWORD_DEFAULT); // Hash the password
                $FullName = $_POST['regFullName'];
                $roleID = $_POST['regRole'];
                $provinceName = ucwords(strtolower(trim($_POST['regProvince']))); // Normalize province input
                $cityName = ucwords(strtolower(trim($_POST['regCity']))); // Normalize city input

                //Insert or retrieve province
                $pdoQueryProvince = "SELECT province_id FROM tbprovince WHERE province_name = :province_name";
                $pdoResultProvince = $pdoConnect->prepare($pdoQueryProvince);
                $pdoResultProvince->execute([':province_name' => $provinceName]);
                $provinceRow = $pdoResultProvince->fetch(PDO::FETCH_ASSOC);

                if ($provinceRow) {
                    $provinceID = $provinceRow['province_id'];
                } else {
                    $pdoInsertProvince = "INSERT INTO tbprovince (province_name) VALUES (:province_name)";
                    $pdoResultInsertProvince = $pdoConnect->prepare($pdoInsertProvince);
                    $pdoResultInsertProvince->execute([':province_name' => $provinceName]);
                    $provinceID = $pdoConnect->lastInsertId();
                }

                // Insert or retrieve city/municipality
                $pdoQueryCity = "SELECT cm_id FROM tbcitymuni WHERE cm_name = :cm_name AND province_id = :province_id";
                $pdoResultCity = $pdoConnect->prepare($pdoQueryCity);
                $pdoResultCity->execute([
                    ':cm_name' => $cityName,
                    ':province_id' => $provinceID
                ]);
                $cityRow = $pdoResultCity->fetch(PDO::FETCH_ASSOC);

                if ($cityRow) {
                    $cityID = $cityRow['cm_id'];
                } else {
                    $pdoInsertCity = "INSERT INTO tbcitymuni (cm_name, province_id) VALUES (:cm_name, :province_id)";
                    $pdoResultInsertCity = $pdoConnect->prepare($pdoInsertCity);
                    $pdoResultInsertCity->execute([
                        ':cm_name' => $cityName,
                        ':province_id' => $provinceID
                    ]);
                    $cityID = $pdoConnect->lastInsertId();
                }
                
                // Insert User
                $pdoQuery = "INSERT INTO tbuser ( UserName, PassWord, FullName, role_id, province_id, cm_id)
                 VALUES ( :UserName, :PassWord, :FullName, :role_id, :province_id, :cm_id )";
                $pdoResult =$pdoConnect->prepare($pdoQuery);
                $pdoExec = $pdoResult->execute([
                    ":UserName" => $UserName,
                    ":PassWord" => $PassWord,
                    ":FullName" => $FullName,
                    ":role_id" => $roleID,
                    ":province_id" => $provinceID,
                    ":cm_id" => $cityID,
                ]);
                
                if ($pdoExec) {
                    $pdoConnect->commit(); // Commit the transaction if all operations succeed
                    header("location:index.php");
                } else {
                    echo 'Failed to register user.';
                }
            }
        } catch (PDOException $error) {
            $pdoConnect->rollBack(); // Roll back changes if any operation fails
            echo "Error: " . $error->getMessage();
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

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

                  <form class="row g-3 needs-validation" method="POST" novalidate>
                    <div class="col-12">
                      <label for="regUserName" class="form-label">Username</label>
                      <input type="text" name="regUserName" class="login-form" id="regUserName" required>
                      <div class="invalid-feedback">Please, enter your username!</div>
                    </div>

                    <div class="col-12">
                      <label for="regFullName" class="form-label">Full Name</label>
                      <input type="text" name="regFullName" class="login-form" id="regFullName" required>
                      <div class="invalid-feedback">Please enter a full name!</div>
                    </div>

                    <div class="col-12">
                      <label for="regPassWord" class="form-label">Password</label>
                      <input type="password" name="regPassWord" class="login-form" id="regPassWord" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                     <label for="dropdown">Choose an option:</label> <br>
                     <select id="dropdown" name="regRole" required class="styled-select">
                     <option value="3">Farmer</option>
                    </select>
                    </div>

                    <div class="col-12">
                      <label for="regFullName" class="form-label">Province</label>
                      <input type="text" name="regProvince" class="login-form" id="regFullName" required>
                      <div class="invalid-feedback">Please enter a valid province!</div>
                    </div>

                    <div class="col-12">
                      <label for="regPassWord" class="form-label">City/Municipality</label>
                      <input type="text" name="regCity" class="login-form" id="regPassWord" required>
                      <div class="invalid-feedback">Please enter a valid city/municipality!</div>
                    </div>
                          
                    <div class="col-12"> <br>
                      <button class="flexible-wide-button" name="register" type="submit">Register</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Already have an account? <a href="index.php">Log in</a></p>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
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



    