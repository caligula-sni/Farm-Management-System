<?php
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['UserName'])) {
        header("location: /wfp/index.php");
        exit;
    }
 
    $userID = $_SESSION['id'];
    $userCMID = $_SESSION['cm_id'];
    $userProvinceID = $_SESSION['province_id'];

    if (isset ($_POST['insert'])) {
    try {

        require_once ("./include/connect/dbcon.php");

        $farmName = $_POST['farmName'];
        $cropNames = $_POST['crops'];
        $cropQuantities = $_POST['quantities'];
        $provinceID = $userProvinceID;

        // Insert farm into tbfarm table
        $pdoQueryFarm = "INSERT INTO tbfarm (id, farm_name, province_id, cm_id)
                         VALUES (:id, :farm_name, :province_id, :cm_id)";
        $pdoResultFarm = $pdoConnect->prepare($pdoQueryFarm);
        $pdoResultFarm->execute([
            'id' => $userID,
            'farm_name' => $farmName,
            'province_id' => $userProvinceID,
            'cm_id' => $userCMID,
        ]);

        // Get the last inserted farm_id
        $farmID = $pdoConnect->lastInsertId();

        // Insert crops and link them to the farm
        foreach ($cropNames as $index => $cropName) {
            // Check if crop already exists
            $pdoCheckQueryCrop = "SELECT * FROM tbcrop WHERE crop_name = :crop_name";
            $pdoCheckResultCrop = $pdoConnect->prepare($pdoCheckQueryCrop);
            $pdoCheckResultCrop->execute(['crop_name' => $cropName]);
            $cropRow = $pdoCheckResultCrop->fetch(PDO::FETCH_ASSOC);

            // If crop doesnt exist, insert it into tbcrop table
            if (!$cropRow) {
                $pdoInsertQueryCrop = "INSERT INTO tbcrop (crop_name) VALUES (:crop_name)";
                $pdoInsetResultCrop = $pdoConnect->prepare($pdoInsertQueryCrop);
                $pdoInsetResultCrop->execute(['crop_name' => $cropName]);

                // Increment the crop_id
                $cropID = $pdoConnect->lastInsertId();
            } else {
                // Crop aready exists, use its crop_id
                $cropID = $cropRow['crop_id'];
            }

            // Insert the farmp-crop relation into tbfarmsupply
            $pdoInsertQuerySupply = "INSERT INTO tbfarmsupply (farm_id, crop_id, fs_quantity)
                                    VALUES (:farm_id, :crop_id, :quantity)";
            $pdoInsertResultSupply = $pdoConnect->prepare($pdoInsertQuerySupply);
            $pdoInsertResultSupply->execute([
                'farm_id' => $farmID,
                'crop_id' => $cropID,
                'quantity' => $cropQuantities[$index],
            ]);
    
        }
        // Redirect to the farm profile page or confirmation page
        header("location: farmsprofile.php?farm_id=" . $farmID);
        exit;
    } catch ( PDOException $error) {
        echo "Error: " . $error->getMessage();
        exit;
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
      <h1>Farm Managment</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Create Farm</li>
        </ol>
      </nav>
    </div>
    
   <div class="container mt-5">
    <h1 class="text-center">Create Your Farm</h1>
    <p class="text-center">Fill out the details to create a new farm and add crops.</p>

    <div class="card shadow-lg p-4">
        <form action="farmforms.php" method="post">
            <input type="hidden" name="Id">

            <div class="mb-3">
                <h4 class="mb-3">Farm Name</h4>
                <input type="text" name="farmName" class="form-control" required placeholder="Enter Farm Name">
            </div>

            <h4 class="mb-3">Enter Your Crops and Quantities</h4>
            <div id="cropInputs">
                <div class="cropInput mb-3">
                    <label for="crops[]" class="form-label">Crop Name</label>
                    <input type="text" name="crops[]" class="form-control" required placeholder="Enter Crop Name">
                    <label for="quantities[]" class="form-label mt-2">Quantity (kg)</label>
                    <input type="text" name="quantities[]" class="form-control" required placeholder="Enter Quantity">
                </div>
            </div>

            <button type="button" id="addCropBtn" class="btn btn-secondary btn-sm mb-3">Add Another Crop</button>
            <button type="submit" name="insert" class="flexible-wide-button2">Create Farm</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('addCropBtn').addEventListener('click', function () {
        const cropInputDiv = document.createElement('div');
        cropInputDiv.classList.add('cropInput', 'mb-3');
        cropInputDiv.innerHTML = `
            <label for="crops[]" class="form-label">Crop Name</label>
            <input type="text" name="crops[]" class="form-control" required placeholder="Enter Crop Name">
            <label for="quantities[]" class="form-label mt-2">Quantity (kg)</label>
            <input type="text" name="quantities[]" class="form-control" required placeholder="Enter Quantity">
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