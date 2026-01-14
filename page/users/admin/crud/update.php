
<?php
            require_once ("../include/connect/dbcon.php");

            session_start();
            if (isset($_SESSION["UserName"])) {
                echo '<h3>Login Success, Welcome - ' . $_SESSION["UserName"] . '</h3>';
            } else {
                header("location: /wfp/index.php");
            }
            if (!empty($_POST["modify"])) {

                // sanitize inputs
                $userName = htmlspecialchars($_POST['User']);
                $password = password_hash($_POST['Pass'], PASSWORD_DEFAULT); // Hash the password
                $fullName = htmlspecialchars($_POST['FName']);
               
                $Province = ucwords(strtolower(trim(htmlspecialchars($_POST['Province']))));
                $CityMuni = ucwords(strtolower(trim(htmlspecialchars($_POST['CityMuni']))));

                // Check if the province exists
                $pdopQueryProvince = "SELECT province_id FROM tbprovince WHERE province_name = :province_name";
                $pdoResultProvince = $pdoConnect->prepare($pdopQueryProvince);
                $pdoResultProvince->execute([':province_name' => $Province]);
                $provinceRow = $pdoResultProvince->fetch(PDO::FETCH_ASSOC);

                if ($provinceRow) {
                    // if province exist, get province_id
                    $provinceID = $provinceRow['province_id'];
                } else {
                    // if province does not exist, insert and increment province_id
                    $pdoQueryInsertProvince = "INSERT INTO tbprovince (province_name) VALUES (:province_name)";
                    $pdoResultInsertProvince = $pdoConnect->prepare($pdoQueryInsertProvince);
                    $pdoResultInsertProvince->execute([':province_name' => $Province]);
                    $provinceID = $pdoConnect->lastInsertId();
                }

                // Check if the city/municipality exists
                $pdoQueryCM = "SELECT cm_id FROM tbcitymuni WHERE cm_name = :cm_name AND province_id = :province_id";
                $pdoResultCM = $pdoConnect->prepare($pdoQueryCM);
                $pdoResultCM->execute([
                    ':cm_name' => $CityMuni,
                    ':province_id' => $provinceID
                ]);
                $cityRow = $pdoResultCM->fetch(PDO::FETCH_ASSOC);

                if ($cityRow) {
                    // if the city/municipality exist, get cm_id
                    $cityID = $cityRow['cm_id'];
                } else {
                    // if the city/municipality does not exist, insert and increment cm_id
                    $pdoQueryInsertCM = "INSERT INTO tbcitymuni (cm_name, province_id) VALUES (:cm_name, :province_id)";
                    $pdoResultInsertCM = $pdoConnect->prepare($pdoQueryInsertCM);
                    $pdoResultInsertCM->execute([
                        ':cm_name' => $CityMuni,
                        ':province_id' => $provinceID
                    ]);
                    $cityID = $pdoConnect->lastInsertId();
                }

                // Use prepared statement with named placeholders
                $pdoQuery = $pdoConnect->prepare("UPDATE tbuser SET UserName = :userName, PassWord = :password, FullName = :fullName,  
                province_id = :provinceID, cm_id = :cityID WHERE id = :id");
                $pdoResult = $pdoQuery->execute(array(
                    ':userName' => $userName,
                    ':password' => $password,
                    ':fullName' => $fullName,
                    
                    ':provinceID' => $provinceID,
                    ':cityID' => $cityID,
                    ':id' => $_GET["id"]
                ));
                if ($pdoResult) {
                    // Log the action in the audit trail
                    $pdoQuery = "INSERT INTO audit_trail(action) VALUES ('User updated')";
                    $pdoResult = $pdoConnect->prepare($pdoQuery);
                    $pdoResult->execute();
                    header('location:read.php');
                }
            }
                // Use prepared Statement for Select query
                $pdoQuery = $pdoConnect->prepare(
                    "SELECT
                        tbuser.*,
                        tbprovince.province_name,
                        tbcitymuni.cm_name
                    FROM tbuser
                    LEFT JOIN tbprovince ON tbuser.province_id = tbprovince.province_id
                    LEFT JOIN tbcitymuni ON tbuser.cm_id = tbcitymuni.cm_id
                     WHERE tbuser.id = :id");

                $pdoQuery->execute(array(':id' => $_GET["id"]));
                $pdoResult = $pdoQuery->fetchAll();
                $pdoConnect = null;
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
<link href="../../../../include/styles/img/favicon.png" rel="icon">
<link href="../../../../include/styles/img/apple-touch-icon.png" rel="apple-touch-icon">

<!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

<!-- Vendor CSS Files -->
<link href="../../../../include/styles/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../../../../include/styles/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="../../../../include/styles/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link href="../../../../include/styles/vendor/quill/quill.snow.css" rel="stylesheet">
<link href="../../../../include/styles/vendor/quill/quill.bubble.css" rel="stylesheet">
<link href="../../../../include/styles/vendor/remixicon/remixicon.css" rel="stylesheet">
<link href="../../../../include/styles/vendor/simple-datatables/style.css" rel="stylesheet">

<!-- Template Main CSS File -->
<link href="../../../../include/styles/style.css" rel="stylesheet">
  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

    
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Crop Monitoring Sys.</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="search.php">
        <input type="text" name="id" placeholder="Search" title="Enter search keyword">
    <button type="submit" name="Find" value="Search" title="Search"><i class="bi bi-search">
    </i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

     
     
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link" href="home.php">
        <span> <?php if (isset($_SESSION['UserName'])): ?>
        Welcome back, <?= $_SESSION['UserName'] ?>!
        <?php endif; ?> </span>
      </a>
    </li> <br>
    <li class="nav-item">
      <a class="nav-link" href="../home.php">
        <i class="bi bi-grid"></i>
        <span>Homepage</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="read.php">
        <i class="bi bi-person"></i>
        <span>Adduser</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../dropdown.php">
        <i class="bi bi-menu-button-wide"></i>
        <span>Dropdown</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../audit_trail.php">
        <i class="bi bi-journal-text"></i>
        <span>Audit Trail</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../logout.php">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>Logout</span>
      </a>
    </li>
  </ul>
</aside>
<!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Local Administration</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Update</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <form action="update.php?id=<?php echo $_GET["id"]; ?>" method="post">
    <div class="row mb-3">
        <label for="inputText" class="col-sm-2 col-form-label">Username</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="User" value="<?php 
            echo $pdoResult[0]['UserName']; ?>"  id="inputText" required 
            placeholder="Username" >
        </div>
    </div>

    <div class="row mb-3">
        <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
        <div class="col-sm-10">
            <input type="password" name="Pass" value="" class="form-control" 
            id="inputPassword" required placeholder="Password">
        </div>
    </div>

    <div class="row mb-3">
        <label for="inputFullName" class="col-sm-2 col-form-label">Fullname</label>
        <div class="col-sm-10">
            <input type="text" name="FName" value="<?php echo $pdoResult[0]['FullName']; ?>" class="form-control" id="inputFullName" required placeholder="Full Name">
        </div>
    </div>

   
    
    

      <div class="row mb-3">
        <label for="inputPassword" class="col-sm-2 col-form-label">Province</label>
        <div class="col-sm-10">
            <input type="text" name="Province" class="form-control" id="inputPassword"
            value="<?php echo $pdoResult[0]['province_name']; ?>" required placeholder="Province">
        </div>
    </div>

    <div class="row mb-3">
        <label for="inputFullName" class="col-sm-2 col-form-label">City/Municipality</label>
        <div class="col-sm-10">
            <input type="text" name="Citymuni" class="form-control" id="inputFullName" 
            value="<?php echo $pdoResult[0]['cm_name']; ?>" required placeholder="City/Municipality" >
        </div>
    </div> 
      

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label"></label>
        <div class="col-sm-10">
            <button type="submit" name="modify" value="Update" class="flexible-wide-button2">Update</button>
        </div>
    </div>
</form>


      

</main>


  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

   <!-- Vendor JS Files -->
  <script src="../../../../include/styles/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../../../../include/styles/vendor/bootstrap/js/bootstrap.bundle.min.js">
  </script>
  <script src="../../../../include/styles/vendor/chart.js/chart.umd.js"></script>
  <script src="../../../../include/styles/vendor/echarts/echarts.min.js"></script>
  <script src="../../../../include/styles/vendor/quill/quill.js"></script>
  <script src="../../../../include/styles/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../../../../include/styles/vendor/tinymce/tinymce.min.js"></script>
  <script src="../../../../include/styles/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../../../../include/styles/main.js"></script>
</body>

</html>