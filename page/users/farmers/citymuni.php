<?php

            require_once("./include/connect/dbcon.php");
            session_start();
            echo "<br/><br />";

            // Validate the user again to prevent unauthorize access - ChatGPT
            if (!isset($_SESSION['UserName'])) {
                header("location: /wfp/index.php");
                exit;
            }

            //Initialize variables
            $cityRow = $cityListRow = null;
            $Province = '';

            // Get the selected province_id from URL
            if (isset($_GET['province_id']) && is_numeric($_GET['province_id'])) {
                $provinceID = intval($_GET['province_id']);

                try {
                    // Fetch the province_name
                    $pdoQueryProvince = "SELECT province_name FROM tbprovince WHERE province_id = :province_id";
                    $pdoResultProvince = $pdoConnect->prepare($pdoQueryProvince);
                    $pdoResultProvince->execute(['province_id' => $provinceID]);
                    $provinceRow = $pdoResultProvince->fetch(PDO::FETCH_ASSOC);
     
                    if (!$provinceRow) {
                        echo "Province not Found";
                        exit;
                    }
        
                    $Province = ucwords(strtolower(trim(htmlspecialchars($provinceRow['province_name']))));
                    
                    // Fetch the cities/municipalities in this province
                    $pdoQueryCM = "SELECT cm_id, cm_name FROM tbcitymuni WHERE province_id = :province_id";
                    $pdoResultCM = $pdoConnect->prepare($pdoQueryCM);
                    $pdoResultCM->execute(['province_id' => $provinceID]);
                    $cityRow = $pdoResultCM->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $error) {
                echo "Error: " . $error->getMessage();
                exit;
            }

        } else if (isset($_GET['cm_id']) && is_numeric($_GET['cm_id'])) {
            $cmID = intval($_GET['cm_id']);
            // Fetch city/municipality details using cm_id
                try {
                    $pdoQueryCM = "SELECT cm_id, cm_name, province_id FROM tbcitymuni WHERE cm_id = :cm_id";
                    $pdoResultCM = $pdoConnect->prepare($pdoQueryCM);
                    $pdoResultCM->execute(['cm_id' => $cmID]);
                    $cityRows = $pdoResultCM->fetch(PDO::FETCH_ASSOC);

                    if (!$cityRows || !is_array($cityRows)) {
                        echo "City/Municipality not Found";
                        exit;
                    }

                    $cityMuni = ucwords(strtolower(trim(htmlspecialchars($cityRows['cm_name']))));
                    $provinceID = $cityRows['province_id'];

                    $pdoQueryProvince = "SELECT province_name FROM tbprovince WHERE province_id = :province_id";
                    $pdoResultProvince = $pdoConnect->prepare($pdoQueryProvince);
                    $pdoResultProvince->execute(['province_id' => $provinceID]);
                    $provinceRow = $pdoResultProvince->fetch(PDO::FETCH_ASSOC);

                    if (!$provinceRow) {
                        echo "Province not Found";
                        exit;
                    }
            
                    $Province = ucwords(strtolower(trim(htmlspecialchars($provinceRow['province_name']))));

                    // Fetch all cities in the same province as the selected city
                    $pdoQueryCM = "SELECT cm_id, cm_name FROM tbcitymuni WHERE province_id = :province_id";
                    $pdoResultCM = $pdoConnect->prepare($pdoQueryCM);
                    $pdoResultCM->execute(['province_id' => $provinceID]);
                    $cityListRow = $pdoResultCM->fetchAll(PDO::FETCH_ASSOC);
                    
            }  catch (PDOException $error) {
                echo "Error: " . $error->getMessage();
            exit;

            } 
        } else {
                echo "Invalid Request";
                exit;
            }


                echo '<h3>Welcome, ' . $_SESSION['UserName'] . '</h3>';
                echo '<br /><br /> <a href="province.php">Province</a>';
                echo '<br /><br /> <a href="home.php">Home</a>';
                echo '<br /><br /> <a href="logout.php">Logout</a>';
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

<<main id="main" class="main">

<div class="pagetitle">
<h1>Farm Management</h1>
<nav>
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="index.html">Home</a></li>
<li class="breadcrumb-item active">City/Municipalitys</li>
</ol>
</nav>
</div><!-- End Page Title -->


<h4 class="text-center">City/Municipality in <?= ucwords(strtolower(trim(htmlspecialchars($Province)))) ?></h4>

<div class="card">
    <div class="card-header text-center">
        <h5 class="card-title">List of Cities/Municipalities</h5>
    </div>
    <div class="card-body">
        <?php if (isset($cityRow) && is_array($cityRow) && count($cityRow) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>City/Municipality Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cityRow as $city): ?>
                        <tr>
                            <td>
                                <a href="farms.php?cm_id=<?= htmlspecialchars($city['cm_id']) ?>" class="btn btn-link">
                                    <?= ucwords(strtolower(trim(htmlspecialchars($city['cm_name'])))) ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($cityRows) && is_array($cityRows)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>City/Municipality Name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="farms.php?cm_id=<?= htmlspecialchars($cityRows['cm_id']) ?>" class="btn btn-link">
                                <?= ucwords(strtolower(trim(htmlspecialchars($cityRows['cm_name'])))) ?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-muted">No cities or municipalities found in the province.</p>
        <?php endif; ?>

        <?php if (isset($cityListRow) && is_array($cityListRow) && count($cityListRow) > 0): ?>
            <div class="mt-4">
                <h5 class="text-center">Other Cities in <?= ucwords(strtolower(trim(htmlspecialchars($Province)))) ?>:</h5>
                <ul class="list-group">
                    <?php foreach ($cityListRow as $otherCity): ?>
                        <?php if ($otherCity['cm_id'] != $cmID): ?>
                            <li class="list-group-item">
                                <a href="farms.php?cm_id=<?= htmlspecialchars($otherCity['cm_id']) ?>" class="btn btn-link">
                                    <?= ucwords(strtolower(trim(htmlspecialchars($otherCity['cm_name'])))) ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
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