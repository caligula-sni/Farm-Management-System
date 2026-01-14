<?php
session_start(); // This must be the first line of PHP code!

// Redirect if not logged in
if (!isset($_SESSION["UserName"])) {
    header("location: index.php");
    exit; // Prevent further execution
}

// Your existing PHP code starts here...
$currentUserId = $_SESSION['id'];
$currentRoleId = $_SESSION['role_id'];
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
    <link href="../../../../include/styles/img/apple-touch-icon.png" rel="apple-touch-icon"><!-- Google Fonts -->
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
            <form class="search-form d-flex align-items-center" method="POST" action="search.php">
                <input type="text" name="id" placeholder="Search" title="Enter search keyword">
                <button type="submit" name="Find" value="Search" title="Search"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle " href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li>
            </ul>
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

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>IT Administration</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active">Search User</li>
                </ol>
            </nav>
        </div>

        

        <?php


        if (isset($_POST["Find"])) {

            //connect to mysql
            try {
                $pdoConnect = new PDO("mysql:host=localhost;dbname=dbfarm2", "root", "");
            } catch (PDOException $exc) {
                echo $exc->getMessage();
                exit();
            }

            // validate and sanitize id input 
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id !== false && $id !== null) {
                
                // mysql search query 
                $pdoQuery = "SELECT * FROM tbuser WHERE id = :id";
                $pdoResult = $pdoConnect->prepare($pdoQuery);

                // set your id to the query id
                $pdoExec = $pdoResult->execute(array(":id" => $id));

                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">Search Results</h5>';
                echo '<table class="table">';
                echo '<thead>';
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>UserName</th>";
                echo "<th>FullName</th>";
                echo "<th>Role_id</th>";
                echo "<th>Province_id</th>";
                echo "<th>CityMuni_id</th>";
                echo "<th>Action</th>";
                echo "</tr>";
                 echo '</thead>';
                    echo '<tbody>';
                if ($pdoExec) {
                    if ($pdoResult->rowCount() > 0) {
                        while ($row = $pdoResult->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        echo "<tr>";
                        echo "<td>$id</td>";
                        echo "<td>$UserName</td>";
                        echo "<td>$FullName</td>";
                        echo "<td>$role_id</td>";
                        echo "<td>$province_id</td>";
                        echo "<td>$cm_id</td>";
                        if ($id == $currentUserId && $currentRoleId == 2) {
                            echo "<td><a href='update.php?id=$id';>Edit</a></td>";
                        }
                        echo "</tr>";
                        }
                    } else {
                        echo '<br><br><br><br><br>';
                        echo 'No Data';
                    }
                }
            } else {
                echo '<br><br><br><br><br>';
                echo 'Invalid ID';
            }
        }
        $pdoConnect = null;
          echo '</tbody>';
      echo '</table>';
   echo '</div>';
 echo '</div>';
    ?>

    </main>

    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        </div>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="../../../../include/styles/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../../../../include/styles/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../../../include/styles/vendor/chart.js/chart.umd.js"></script>
    <script src="../../../../include/styles/vendor/echarts/echarts.min.js"></script>
    <script src="../../../../include/styles/vendor/quill/quill.js"></script>
    <script src="../../../../include/styles/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../../../../include/styles/vendor/tinymce/tinymce.min.js"></script>
    <script src="../../../../include/styles/vendor/php-email-form/validate.js"></script>
    <script src="../../../../include/styles/main.js"></script>
</body>

</html