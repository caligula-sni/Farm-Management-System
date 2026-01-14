<?php
    require_once ("./include/connect/dbcon.php");
    session_start();

    if (!isset($_SESSION['UserName']) || !isset($_GET['farm_id'])) {
        header("location: /wfp/index.php");
        exit;
    }

    $farmID = $_GET['farm_id'];

    if (isset($_POST['insert'])) {

        // Get the crop names and quantities from the form
        $crops = $_POST['crops'];
        $quantities = $_POST['quantities'];

        // Array to store error messages for duplicate crops
        $duplicateCrops = [];

        try {
            
            $pdoConnect->beginTransaction();

            for ($i = 0; $i < count($crops); $i++) {
                $cropName = $crops[$i];
                $quantity = $quantities[$i];
            
                // Get crop_id from the tbcrop based on crop_name
                $pdoQueryCropId = "SELECT crop_id FROM tbcrop WHERE crop_name = :crop_name";
                $pdoResultCropId = $pdoConnect->prepare($pdoQueryCropId);
                $pdoResultCropId->execute([':crop_name' => $cropName]);
                $cropId = $pdoResultCropId->fetchColumn();

                if (!$cropId) {
                    // If the crop does not exist, insert it into tbcrop table
                    $pdoInsertQueryCrop = "INSERT INTO tbcrop (crop_name) VALUES (:crop_name)";
                    $pdoInsertResultCrop = $pdoConnect->prepare($pdoInsertQueryCrop);
                    $pdoInsertResultCrop->execute(['crop_name' => $cropName]);

                    // Get the crop_id after inserting
                    $cropId = $pdoConnect->lastInsertId();
                }

            // Check if the crop already exists in the farm
            $pdoQueryCheckCrop = "SELECT COUNT(*) FROM tbfarmsupply
                                WHERE farm_id = :farm_id AND crop_id = :crop_id";
            $pdoResultCheckCrop = $pdoConnect->prepare($pdoQueryCheckCrop);
            $pdoResultCheckCrop->execute([
                ':farm_id' => $farmID,
                ':crop_id' => $cropId
            ]);
            $cropExists = $pdoResultCheckCrop->fetchColumn();

            if ($cropExists > 0 ) {
                // If the crop exists, add it to the duplicateCrops array
                $duplicateCrops[] = $cropName;
        } else {
            // If the crop doesn't exist, insert the new crop
            $pdoInsertQueryCrop = "INSERT INTO tbfarmsupply (farm_id, crop_id, fs_quantity) 
                                    VALUES (:farm_id, :crop_id, :quantity)";
            $pdoInsertResultCrop = $pdoConnect->prepare($pdoInsertQueryCrop);
            $pdoInsertResultCrop->execute([
                ':farm_id' => $farmID,
                ':crop_id' => $cropId,
                ':quantity' => $quantity
            ]);
        }
    }

     // Commit the transaction
     $pdoConnect->commit();

     // If there are duplicate crops, show a message to the user
     if (count($duplicateCrops) > 0) {
        echo "The following crops already exist in your farm: " . implode(", ", $duplicateCrops) . ". Please modify or remove them from the form.";
     } else {
        // If no duplicates, redirect to the farm profile page
        header("location: farmsprofile.php?farm_id=" . $farmID);
        exit;
     }
    } catch (PDOException $error) {
        // Rollback transaction in case of an error
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
<link href="../../../../include/styles/img/favicon.png" rel="icon">
<link href="../../../../include/styles/img/apple-touch-icon.png" rel="apple-touch-icon">

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
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="
      ../admin/crud/search.php">
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
        </li>
    </nav>

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
<li class="breadcrumb-item active">New Crop</li>
</ol>
</nav>
</div><!-- End Page Title -->

   <div class="card">
    <div class="card-header text-center">
        <h5 class="card-title">Enter Your Crops and Quantities</h5>
    </div>
    <div class="card-body">
        <form action="newcrop.php?farm_id=<?= htmlspecialchars($farmID) ?>" method="POST">
            <div id="cropInputs">
                <div class="form-group">
                    <label for="cropName1">Crop Name:</label>
                    <input type="text" id="cropName1" name="crops[]" class="form-control" required placeholder="Enter crop name">
                </div>
                <div class="form-group">
                    <label for="quantity1">Quantity (kg):</label>
                    <input type="number" id="quantity1" name="quantities[]" class="form-control" required placeholder="Enter quantity in kg">
                </div>
            </div>
            <div class="text-center mt-3">
                <button type="button" id="addCropBtn" class="btn btn-success">Add Another Crop</button>
            </div>
            <div class="text-center mt-4">
                <button type="submit" name="insert" class="btn btn-success">Add Crops</button>
            </div>
        </form>
    </div>
</div>
<div class="text-center mt-3">
            <a href="farmsprofile.php?farm_id=<?= htmlspecialchars($farmID) ?>" class="btn btn-link">Back to Farm Profile</a>
        </div>

<script>
    document.getElementById('addCropBtn').addEventListener('click', function () {
        const cropInputDiv = document.createElement('div');
        cropInputDiv.classList.add('form-group', 'mt-3');
        cropInputDiv.innerHTML = `
            <label for="cropNameNew">Crop Name:</label>
            <input type="text" name="crops[]" class="form-control" required placeholder="Enter crop name">
            <label for="quantityNew" class="mt-2">Quantity (kg):</label>
            <input type="number" name="quantities[]" class="form-control" required placeholder="Enter quantity in kg">
        `;
        document.getElementById('cropInputs').appendChild(cropInputDiv);
    });
</script>


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