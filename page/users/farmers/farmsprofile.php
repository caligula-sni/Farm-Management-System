<?php
    require_once ("./include/connect/dbcon.php");
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

    try {
        //fetch farm details
        $pdoQueryFarm = "SELECT f.farm_name, u.UserName, u.id, c.cm_name, p.province_name
                        FROM tbfarm f
                        INNER JOIN tbuser u ON f.id = u.id
                        INNER JOIN tbcitymuni c ON f.cm_id = c.cm_id
                        INNER JOIN tbprovince p ON f.province_id = p.province_id
                        WHERE f.farm_id = :farm_id";
        $pdoResultFarm = $pdoConnect->prepare($pdoQueryFarm);
        $pdoResultFarm->execute(['farm_id' => $farmID]);
        $farmRow = $pdoResultFarm->fetch(PDO::FETCH_ASSOC);

        // fetch associated crops
        $pdoQueryCrops = "SELECT cs.crop_name, fs.fs_quantity
                        FROM tbfarmsupply fs
                        INNER JOIN tbcrop cs ON fs.crop_id = cs.crop_id
                        WHERE fs.farm_id = :farm_id";
        $pdoResultCrops = $pdoConnect->prepare($pdoQueryCrops);
        $pdoResultCrops->execute(['farm_id' => $farmID]);
        $cropRow = $pdoResultCrops->fetchAll(PDO::FETCH_ASSOC);

        // Check if the logged-in user is the owner of the farm
        $isOwner = $farmRow['id'] == $loggedInUserID;

    } catch (PDOException $error) {
        echo "Error: " . $error->getMessage();
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
<a class="nav-link" href="home.php">
<i class="bi bi-journal-text"></i>
<span>Home</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link" href="province.php">
<i class="bi bi-journal-text"></i>
<span>Province</span>
</a>
</li>
</li> 
<li class="nav-item">
<a class="nav-link" href="farms.php">
<i class="bi bi-journal-text"></i>
<span>Farms</span>
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
<h1>Farm Management</h1>
<nav>
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="index.html">Home</a></li>
<li class="breadcrumb-item active">Profile</li>
</ol>
</nav>
</div><!-- End Page Title -->

<div class="container mt-5">
    <h1 class="text-center">Farm Profile</h1>

    <div class="card shadow-lg p-4">
        <div class="mb-4">
            <h4 class="mb-3">Farm Details</h4>
            <p><strong>Farm Name:</strong> <?= ucwords(strtolower(trim(htmlspecialchars($farmRow['farm_name'])))) ?></p>
            <p><strong>Owner:</strong> <?= ucwords(strtolower(trim(htmlspecialchars($farmRow['UserName'])))) ?></p>
            <p><strong>City/Municipality:</strong> <?= ucwords(strtolower(trim(htmlspecialchars($farmRow['cm_name'])))) ?></p>
            <p><strong>Province:</strong> <?= ucwords(strtolower(trim(htmlspecialchars($farmRow['province_name'])))) ?></p>
        </div>

        <div class="mb-4">
            <h4 class="mb-3">Crops</h4>
            <?php if (count($cropRow) > 0): ?>
                <ul class="list-group">
                    <?php foreach ($cropRow as $crop): ?>
                        <li class="list-group-item">
                            <strong><?= ucwords(strtolower(trim(htmlspecialchars($crop['crop_name'])))) ?></strong>
                            <span class="text-muted"> - Quantity (kg): <?= htmlspecialchars($crop['fs_quantity']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-danger">No crops added yet.</p>
            <?php endif; ?>
        </div>

        <?php if ($isOwner): ?>
            <div class="d-flex justify-content-center">
                <a href="newcrop.php?farm_id=<?= htmlspecialchars($farmID) ?>" class="btn btn-secondary">Add New Crop</a>
                <a href="updatecrop.php?farm_id=<?= htmlspecialchars($farmID) ?>" class="btn btn-secondary">Update Crop Quantity</a>
            </div>
        <?php endif; ?>
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