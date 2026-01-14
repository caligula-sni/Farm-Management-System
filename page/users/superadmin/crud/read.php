

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

      <?php
            require_once ("../include/connect/dbcon.php");

            session_start();
            if (isset($_SESSION['UserName'])) {
                echo '<h3>Welcome, ' . $_SESSION['UserName'] . '</h3>';
            } else {
                header("location: testWPF/index.php");
            }
        ?>


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
      <h1>IT Administration</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Add User</li>
        </ol>
      </nav>
    </div>
    
    <form action="create.php" method="POST">
    <div class="row mb-3">
        <label for="inputText" class="col-sm-2 col-form-label">Username</label>
        <div class="col-sm-10">
            <input type="text" name="User" class="form-control" id="inputText">
        </div>
    </div>

    <div class="row mb-3">
        <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
        <div class="col-sm-10">
            <input type="password" name="Pass" class="form-control" id="inputPassword">
        </div>
    </div>

    <div class="row mb-3">
        <label for="inputFullName" class="col-sm-2 col-form-label">Fullname</label>
        <div class="col-sm-10">
            <input type="text" name="FName" class="form-control" id="inputFullName">
        </div>
    </div> 

    <div class="col-12">
      <label for="dropdown">Type of User:</label> <br>
      <select id="dropdown" name="Role" class="styled-select">
      <option value="2">Admin</option>
      <option value="3">Farmer</option>
      </select>
      </div>

      <div class="row mb-3">
        <label for="inputPassword" class="col-sm-2 col-form-label">Province</label>
        <div class="col-sm-10">
            <input type="text" name="Province" class="form-control" id="inputPassword">
        </div>
    </div>

    <div class="row mb-3">
        <label for="inputFullName" class="col-sm-2 col-form-label">City/Province</label>
        <div class="col-sm-10">
            <input type="text" name="Citymuni" class="form-control" id="inputFullName">
        </div>
    </div> 
      
    <div class="row mb-3">
        <label class="col-sm-2 col-form-label"></label>
        <div class="col-sm-10">
            <button type="submit" value="Save" name="insert" class="flexible-wide-button2">Insert</button>
        </div>
    </div>
</form>



<?php
$pdoQuery = "SELECT * FROM tbuser";
$pdoResult = $pdoConnect->prepare($pdoQuery);
$pdoResult->execute();

echo '<div class="card">';
echo '<div class="card-body">';
echo '<h5 class="card-title">Manage Users</h5>';
echo '<!-- Default Table -->';
echo '<table class="table">';
echo '<thead>';
echo '<tr>';
echo '<th>ID</th>';
echo '<th>UserName</th>';
echo '<th>FullName</th>';
echo "<th>Role_id</th>";
echo "<th>Province_id</th>";
echo "<th>City/Muni_id</th>";
echo "<th>Action</th>";
echo '</tr>';
echo '</thead>';
echo '<tbody>';

while ($row = $pdoResult->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    echo '<tr>';
    echo "<td scope=\"col\">$id</td>";
    echo "<td scope=\"col\">$UserName</td>";
    echo "<td scope=\"col\">$FullName</td>";
    echo "<td scope=\"col\">$role_id</td>";
    echo "<td scope=\"col\">$province_id</td>";
    echo "<td scope=\"col\">$cm_id</td>";
    echo "<td><a href='update.php?id=$id';>Edit</a> | <a href='delete.php?id=$id';?>Delete</a></td>";
    echo "<tr>";
}

echo '</tbody>';
echo '</table>';
echo '</div>';
echo '</div>';
?>

                

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