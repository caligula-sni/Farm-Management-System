

    <?php
    session_start();

    if (!isset($_SESSION["UserName"])) {
        header("location:index.php");
        exit;
    }

        require_once ("././././include/connect/dbcon.php");
        $UserName = $_SESSION["UserName"];

        try {
            $pdoQuery = "SELECT * FROM tbuser WHERE UserName = :UserName";
            $pdoResult = $pdoConnect->prepare($pdoQuery);
            $pdoResult->execute(['UserName' => $UserName]);
            $user = $pdoResult->fetch();

            $_SESSION['id'] = $user['id'];
            $_SESSION['role_id'] = $user['role_id'];

        } catch (PDOException $error) {
            echo $error->getMessage();
            exit;
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
<link href="../../../include/styles/img/favicon.png" rel="icon">
<link href="../../../include/styles/img/apple-touch-icon.png" rel="apple-touch-icon">

<!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
<!-- Vendor CSS Files -->
<link href="../../../include/styles/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../../../include/styles/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="../../../include/styles/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link href="../../../include/styles/vendor/quill/quill.snow.css" rel="stylesheet">
<link href="../../../include/styles/vendor/quill/quill.bubble.css" rel="stylesheet">
<link href="../../../include/styles/vendor/remixicon/remixicon.css" rel="stylesheet">
<link href="../../../include/styles/vendor/simple-datatables/style.css" rel="stylesheet">

<!-- Template Main CSS File -->
<link href="../../../include/styles/style.css" rel="stylesheet">


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
    </div>

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST"
       action="crud/search.php">
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
      <a class="nav-link" href="home.php">
        <i class="bi bi-grid"></i>
        <span>Homepage</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="crud/read.php">
        <i class="bi bi-person"></i>
        <span>Adduser</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="dropdown.php">
        <i class="bi bi-menu-button-wide"></i>
        <span>Dropdown</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="audit_trail.php">
        <i class="bi bi-journal-text"></i>
        <span>Audit Trail</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="logout.php">
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
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    

  </main><!-- End #main -->

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
  <script src="../../../include/styles/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../../../include/styles/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../../include/styles/vendor/chart.js/chart.umd.js"></script>
  <script src="../../../include/styles/vendor/echarts/echarts.min.js"></script>
  <script src="../../../include/styles/vendor/quill/quill.js"></script>
  <script src="../../../include/styles/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../../../include/styles/vendor/tinymce/tinymce.min.js"></script>
  <script src="../../../include/styles/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../../../include/styles/main.js"></script>

</body>

</html>