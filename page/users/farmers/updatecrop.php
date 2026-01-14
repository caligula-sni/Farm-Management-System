<?php
    require_once("./include/connect/dbcon.php");
    session_start();

    if (!isset($_SESSION['UserName'])) {
        header("location: /wfp/index.php");
        exit;
    } 

    if (!isset($_GET['farm_id'])) {
        echo "Invalid Farm ID";
        exit;
    }

    $farmID = $_GET['farm_id'];
    $loggedInUserID = $_SESSION['id'];

    // fetch farm details
    try {
        // Fetch the farm's crops and their quantities
        $pdoQueryCropsQuan = "SELECT tbcrop.crop_name, tbfarmsupply.fs_quantity, tbfarmsupply.fs_id
                              FROM tbfarmsupply
                              INNER JOIN tbcrop ON tbfarmsupply.crop_id = tbcrop.crop_id
                              WHERE tbfarmsupply.farm_id = :farm_id";
        $pdoResultCropsQuan = $pdoConnect->prepare($pdoQueryCropsQuan);
        $pdoResultCropsQuan->execute(['farm_id' => $farmID]);
        $cropquanRow = $pdoResultCropsQuan->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $error) {
        echo "Error: " . $error->getMessage();
    }

    if (count($cropquanRow) == 0) {
        echo "No Crops available to update";
        exit;
    }

    // Update crop quantities
    if (isset($_POST['modify'])) {
        try {
            $pdoConnect->beginTransaction();

            foreach ($_POST['quantities'] as $fs_id => $quantity) {
                $pdoQueryQuanUpdate = "UPDATE tbfarmsupply SET fs_quantity = :quantity WHERE fs_id = :fs_id";
                $pdoResultQuanUpdate = $pdoConnect->prepare($pdoQueryQuanUpdate);
                $pdoResultQuanUpdate->execute([
                    ':quantity' => $quantity,
                    ':fs_id' => $fs_id
                ]);
            }

            // Commit the transaction
            $pdoConnect->commit();
            
            header("location: farmsprofile.php?farm_id=" . $farmID);
            exit;
            
        } catch (PDOException $error) {
            
            // Rollback the transaction if an error occurs
            $pdoConnect->rollBack();
            echo "Error: " . $error->getMessage();
        }
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
      <form class="search-form d-flex align-items-center" method="POST" action="
      ../admin/crud/search.php">
        <input type="text" name="id" placeholder="Search" title="Enter search keyword">
    <button type="submit" name="Find" value="Search" title="Search"><i class="bi bi-search">
    </i></button>
      </form>
    </div>

<nav class="header-nav ms-auto">
<ul class="d-flex align-items-center">

<li class="nav-item d-block d-lg-none">
<a class="nav-link nav-icon search-bar-toggle " href="#">
<i class="bi bi-search"></i>
</a>
</li>
</nav>
</header>

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
<a class="nav-link" href="farmsprofile.php">
<i class="bi bi-journal-text"></i>
<span>Profile</span>
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
<h1>Farm Management</h1>
<nav>
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="index.html">Home</a></li>
<li class="breadcrumb-item active">Update Crop</li>
</ol>
</nav>
</div><!-- End Page Title -->
<br> <br>

 <div class="card">
    <div class="card-header text-center">
        <h5 class="card-title">Update Crop Quantities</h5>
    </div>
    <div class="card-body">
        <form action="updatecrop.php?farm_id=<?= htmlspecialchars($farmID) ?>" method="POST">
            <?php foreach ($cropquanRow as $crop): ?>
                <div class="form-group">
                    <label for="quantity<?= $crop['fs_id'] ?>"><?= ucwords(strtolower(trim(htmlspecialchars($crop['crop_name'])))) ?>:</label>
                    <input type="number" id="quantity<?= $crop['fs_id'] ?>" name="quantities[<?= $crop['fs_id'] ?>]" class="form-control" value="<?= htmlspecialchars($crop['fs_quantity']) ?>" required>
                </div>
            <?php endforeach; ?>
            <div class="text-center mt-4">
                <button type="submit" name="modify" class="flexible-wide-button2">Update Quantities</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <a href="farmsprofile.php?farm_id=<?= htmlspecialchars($farmID) ?>" class="btn btn-link">Back to Farm Profile</a>
        </div>
    </div>
</div>



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