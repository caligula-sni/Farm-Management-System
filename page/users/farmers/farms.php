<?php
    require_once ("./include/connect/dbcon.php");
    session_start();

    if (!isset($_SESSION['UserName'])) {
        header("location: /wfp/index.php");
        exit;
    }

    // Get the logged-in user's ID and their cm_id
    $userID = $_SESSION['id'];
    $userCMID = $_SESSION['cm_id'];

    // Get the `cm_id` from the URL
    $cmID = isset($_GET['cm_id']) && is_numeric($_GET['cm_id']) ? intval($_GET['cm_id']) : $userCMID;
    
    try {
        // fetch municipality/city name
        $pdoQueryCity = "SELECT cm_name FROM tbcitymuni WHERE cm_id = :cm_id";
        $pdoResultCity = $pdoConnect->prepare($pdoQueryCity);
        $pdoResultCity->execute(['cm_id' => $cmID]);
        $cityRow = $pdoResultCity->fetch(PDO::FETCH_ASSOC);

        if (!$cityRow) {
            echo "City/Municipality not Found";
            exit;
        }

        $cityName = ucwords(strtolower(trim(htmlspecialchars($cityRow['cm_name']))));

        // Fetch farms under this city/municipality 
        $pdoQueryFarms = "SELECT
                            tbfarm.farm_id,
                            tbfarm.farm_name,
                            tbuser.UserName
                          FROM tbfarm
                          INNER JOIN tbuser ON tbfarm.id = tbuser.id
                          WHERE tbfarm.cm_id = :cm_id";
        $pdoResultFarms = $pdoConnect->prepare($pdoQueryFarms);
        $pdoResultFarms->execute(['cm_id' => $cmID]);
        $farmRow = $pdoResultFarms->fetchAll(PDO::FETCH_ASSOC);

        echo "<br />";
        echo '<h3>Welcome, ' . $_SESSION['UserName'] . '</h3>';
        echo '<br /><br /> <a href="province.php">Province</a>';
        echo '<br /><br /> <a href="citymuni.php?cm_id=' . htmlspecialchars($userCMID) . '">City/Municipality</a>';
        echo '<br /><br /> <a href="home.php">Home</a>';
        echo '<br /><br /> <a href="logout.php">Logout</a>';   

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
<li class="breadcrumb-item active">Farms</li>
</ol>
</nav>
</div>

<div class = "card">
        </div>
             <div class = "fcontainer">
                    <h4>Farms in <?php echo ucwords(strtolower(trim(htmlspecialchars($cityName)))); ?></h4>

                    <ul>
                        <?php if (count($farmRow) > 0 ): ?>
                        <?php foreach ($farmRow as $farm): ?>
                            <li>
                                <a href ="farmsprofile.php?farm_id=<?= htmlspecialchars($farm['farm_id']); ?>">
                                    <?= ucwords(strtolower(trim(htmlspecialchars($farm['farm_name'])))); ?>
                                </a>
                                - Created by: <?= htmlspecialchars($farm['UserName']); ?>
                            </li>
                        <?php endforeach; ?>
                            <?php else: ?>
                                <li>No farms found in <?php echo ucwords(strtolower(trim(htmlspecialchars($cityName)))); ?>.</li>
                            <?php endif; ?>
                    </ul>

                    <br>

                    <?php if ($cmID == $userCMID): ?>

                        <?php
                        // Check if the user owns any farms
                        $userFarm = array_filter($farmRow, function ($farm) use ($userID) {
                            return $farm['UserName'] === $_SESSION['UserName'];
                        });

                        if ($userFarm):
                            $userFarmName = ucwords(strtolower(trim(htmlspecialchars(current($userFarm)['farm_name']))));
                        ?>
                            <p>You already own a farm: <?= ucwords(strtolower(trim(htmlspecialchars($userFarmName)))); ?></p>
                            <p>You can only create a farm in your assigned city/municipality.</p>
                        <?php else: ?>
                            <a href="farmforms.php">Create a Farm in <?php echo ucwords(strtolower(trim(htmlspecialchars($cityName)))); ?></a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>You can only create a farm in your assigned city/municipality.</p>
                    <?php endif; ?>
                </div>
        </body>
        </html>

<?php

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
               
?>


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