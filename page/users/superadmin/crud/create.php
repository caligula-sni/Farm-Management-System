<?php
require_once("../include/connect/dbcon.php");
session_start();

if (!isset($_SESSION['UserName']) || $_SESSION['role_id'] != 3) {
    header("location: ../../../../index.php");
    exit;
}

$message = '';
$messageType = '';

if (isset($_POST['insert'])) {
    try {
        $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $fields = ['User', 'Pass', 'FName', 'Role', 'Province', 'CityMuni'];
        foreach ($fields as $f) {
            if (empty(trim($_POST[$f]))) {
                throw new Exception("All fields are required.");
            }
        }

        $User      = trim($_POST['User']);
        $Pass      = password_hash($_POST['Pass'], PASSWORD_DEFAULT);
        $Fname     = trim($_POST['FName']);
        $Role      = (int) $_POST['Role'];
        $Province  = ucwords(strtolower(trim($_POST['Province'])));
        $CityMuni  = ucwords(strtolower(trim($_POST['CityMuni'])));

        // Duplicate username check
        $chk = $pdoConnect->prepare("SELECT id FROM tbuser WHERE UserName = :u");
        $chk->execute([':u' => $User]);
        if ($chk->fetch()) {
            throw new Exception("Username already exists.");
        }

        $pdoConnect->beginTransaction();

        // Province
        $s = $pdoConnect->prepare("SELECT province_id FROM tbprovince WHERE province_name = :p");
        $s->execute([':p' => $Province]);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $provinceID = $row['province_id'];
        } else {
            $ins = $pdoConnect->prepare("INSERT INTO tbprovince (province_name) VALUES (:p)");
            $ins->execute([':p' => $Province]);
            $provinceID = $pdoConnect->lastInsertId();
        }

        // City
        $s = $pdoConnect->prepare("SELECT cm_id FROM tbcitymuni WHERE cm_name = :c AND province_id = :p");
        $s->execute([':c' => $CityMuni, ':p' => $provinceID]);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $cityID = $row['cm_id'];
        } else {
            $ins = $pdoConnect->prepare("INSERT INTO tbcitymuni (cm_name, province_id) VALUES (:c, :p)");
            $ins->execute([':c' => $CityMuni, ':p' => $provinceID]);
            $cityID = $pdoConnect->lastInsertId();
        }

        // Insert user
        $pdoQuery = "INSERT INTO tbuser (UserName, PassWord, FullName, role_id, province_id, cm_id)
                     VALUES (:User, :Pass, :FName, :Role, :provinceID, :cityID)";
        $pdoResult = $pdoConnect->prepare($pdoQuery);
        $pdoResult->execute([
            ":User"       => $User,
            ":Pass"       => $Pass,
            ":FName"      => $Fname,
            ":Role"       => $Role,
            ":provinceID" => $provinceID,
            ":cityID"     => $cityID,
        ]);

        // Audit trail (include 'user' column — it is NOT NULL)
        $audit = $pdoConnect->prepare("INSERT INTO audit_trail (action, user) VALUES ('User Created', :user)");
        $audit->execute([':user' => $_SESSION['UserName']]);

        $pdoConnect->commit();
        header("Location: read.php");
        exit;

    } catch (Exception $e) {
        if ($pdoConnect->inTransaction()) $pdoConnect->rollBack();
        $message = $e->getMessage();
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Farm Management System</title>
  <link href="../../../../include/styles/img/favicon.png" rel="icon">
  <link href="../../../../include/styles/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../../../../include/styles/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../../include/styles/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../../../../include/styles/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../../../../include/styles/style.css" rel="stylesheet">
</head>
<body>

  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="../home.php" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block">Farm Management System</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="search.php">
        <input type="text" name="id" placeholder="Search" title="Enter search keyword">
        <button type="submit" name="Find" value="Search"><i class="bi bi-search"></i></button>
      </form>
    </div>
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link" href="../home.php">
          <i class="bi bi-grid"></i><span>Homepage</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="read.php">
          <i class="bi bi-person"></i><span>User Management</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="create.php">
          <i class="bi bi-person-plus"></i><span>User Registry</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../dropdown.php">
          <i class="bi bi-menu-button-wide"></i><span>Dropdown</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../audit_trail.php">
          <i class="bi bi-journal-text"></i><span>Audit Trail</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../logout.php">
          <i class="bi bi-box-arrow-in-right"></i><span>Logout</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Chief System Administration</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../home.php">Home</a></li>
          <li class="breadcrumb-item active">Add User</li>
        </ol>
      </nav>
    </div>

    <?php if (!empty($message)): ?>
      <div class="alert alert-<?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Username</label>
        <div class="col-sm-10">
          <input type="text" name="User" class="form-control" value="<?= htmlspecialchars($_POST['User'] ?? '') ?>">
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Password</label>
        <div class="col-sm-10">
          <input type="password" name="Pass" class="form-control">
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Full Name</label>
        <div class="col-sm-10">
          <input type="text" name="FName" class="form-control" value="<?= htmlspecialchars($_POST['FName'] ?? '') ?>">
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Type of User</label>
        <div class="col-sm-10">
          <select name="Role" class="styled-select">
            <option value="1">Farmer</option>
            <option value="2">Admin</option>
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Province</label>
        <div class="col-sm-10">
          <input type="text" name="Province" class="form-control" value="<?= htmlspecialchars($_POST['Province'] ?? '') ?>">
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">City / Municipality</label>
        <div class="col-sm-10">
          <input type="text" name="CityMuni" class="form-control" value="<?= htmlspecialchars($_POST['CityMuni'] ?? '') ?>">
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label"></label>
        <div class="col-sm-10">
          <button type="submit" name="insert" class="flexible-wide-button2">Insert</button>
        </div>
      </div>
    </form>
  </main>

  <footer id="footer" class="footer">
    <div class="copyright">&copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved</div>
    <div class="credits">Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a></div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="../../../../include/styles/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../../../include/styles/main.js"></script>
</body>
</html>
