<?php
session_start();
require_once './include/connect/dbcon.php';

$message = '';
$messageType = '';

if (isset($_POST["register"])) {
    try {
        $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Validate required fields
        $fields = ['regUserName', 'regPassWord', 'regFullName', 'regRole', 'regProvince', 'regCity'];
        foreach ($fields as $field) {
            if (empty(trim($_POST[$field]))) {
                throw new Exception("All fields are required.");
            }
        }

        $UserName     = trim($_POST['regUserName']);
        $PassWord     = password_hash($_POST['regPassWord'], PASSWORD_DEFAULT);
        $FullName     = trim($_POST['regFullName']);
        $roleID       = (int) $_POST['regRole'];
        $provinceName = ucwords(strtolower(trim($_POST['regProvince'])));
        $cityName     = ucwords(strtolower(trim($_POST['regCity'])));

        // Check for duplicate username
        $chkQuery = "SELECT id FROM tbuser WHERE UserName = :UserName";
        $chkStmt  = $pdoConnect->prepare($chkQuery);
        $chkStmt->execute([':UserName' => $UserName]);
        if ($chkStmt->fetch()) {
            throw new Exception("Username already exists. Please choose a different username.");
        }

        $pdoConnect->beginTransaction();

        // Insert or retrieve province
        $pdoQueryProvince = "SELECT province_id FROM tbprovince WHERE province_name = :province_name";
        $pdoResultProvince = $pdoConnect->prepare($pdoQueryProvince);
        $pdoResultProvince->execute([':province_name' => $provinceName]);
        $provinceRow = $pdoResultProvince->fetch(PDO::FETCH_ASSOC);

        if ($provinceRow) {
            $provinceID = $provinceRow['province_id'];
        } else {
            $pdoInsertProvince = "INSERT INTO tbprovince (province_name) VALUES (:province_name)";
            $pdoResultInsertProvince = $pdoConnect->prepare($pdoInsertProvince);
            $pdoResultInsertProvince->execute([':province_name' => $provinceName]);
            $provinceID = $pdoConnect->lastInsertId();
        }

        // Insert or retrieve city/municipality
        $pdoQueryCity = "SELECT cm_id FROM tbcitymuni WHERE cm_name = :cm_name AND province_id = :province_id";
        $pdoResultCity = $pdoConnect->prepare($pdoQueryCity);
        $pdoResultCity->execute([':cm_name' => $cityName, ':province_id' => $provinceID]);
        $cityRow = $pdoResultCity->fetch(PDO::FETCH_ASSOC);

        if ($cityRow) {
            $cityID = $cityRow['cm_id'];
        } else {
            $pdoInsertCity = "INSERT INTO tbcitymuni (cm_name, province_id) VALUES (:cm_name, :province_id)";
            $pdoResultInsertCity = $pdoConnect->prepare($pdoInsertCity);
            $pdoResultInsertCity->execute([':cm_name' => $cityName, ':province_id' => $provinceID]);
            $cityID = $pdoConnect->lastInsertId();
        }

        // Insert user
        $pdoQuery = "INSERT INTO tbuser (UserName, PassWord, FullName, role_id, province_id, cm_id)
                     VALUES (:UserName, :PassWord, :FullName, :role_id, :province_id, :cm_id)";
        $pdoResult = $pdoConnect->prepare($pdoQuery);
        $pdoResult->execute([
            ":UserName"    => $UserName,
            ":PassWord"    => $PassWord,
            ":FullName"    => $FullName,
            ":role_id"     => $roleID,
            ":province_id" => $provinceID,
            ":cm_id"       => $cityID,
        ]);

        $pdoConnect->commit();
        header("location: index.php?registered=1");
        exit;

    } catch (Exception $e) {
        if ($pdoConnect->inTransaction()) {
            $pdoConnect->rollBack();
        }
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
  <title>Farm Management System - Register</title>
  <link href="include/styles/img/favicon.png" rel="icon">
  <link href="include/styles/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="include/styles/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="include/styles/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="include/styles/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="include/styles/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="include/styles/style.css" rel="stylesheet">
</head>
<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.php" class="logo2 d-flex align-items-center w-auto">
                  <span class="d-none d-lg-block"><b>Farm Management System</b></span>
                </a>
              </div>

              <?php if (!empty($message)): ?>
                <div class="alert alert-<?= htmlspecialchars($messageType) ?> w-100" role="alert">
                  <?= htmlspecialchars($message) ?>
                </div>
              <?php endif; ?>

              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter the following details</p>
                  </div>

                  <form class="row g-3 needs-validation" method="POST" novalidate>
                    <div class="col-12">
                      <label for="regUserName" class="form-label">Username</label>
                      <input type="text" name="regUserName" class="login-form" id="regUserName"
                             value="<?= htmlspecialchars($_POST['regUserName'] ?? '') ?>" required>
                      <div class="invalid-feedback">Please enter a username.</div>
                    </div>

                    <div class="col-12">
                      <label for="regFullName" class="form-label">Full Name</label>
                      <input type="text" name="regFullName" class="login-form" id="regFullName"
                             value="<?= htmlspecialchars($_POST['regFullName'] ?? '') ?>" required>
                      <div class="invalid-feedback">Please enter your full name.</div>
                    </div>

                    <div class="col-12">
                      <label for="regPassWord" class="form-label">Password</label>
                      <input type="password" name="regPassWord" class="login-form" id="regPassWord" required>
                      <div class="invalid-feedback">Please enter a password.</div>
                    </div>

                    <div class="col-12">
                      <label for="dropdown" class="form-label">Account Type</label><br>
                      <select id="dropdown" name="regRole" required class="styled-select">
                        <option value="1">Farmer</option>
                      </select>
                    </div>

                    <div class="col-12">
                      <label for="regProvince" class="form-label">Province</label>
                      <input type="text" name="regProvince" class="login-form" id="regProvince"
                             value="<?= htmlspecialchars($_POST['regProvince'] ?? '') ?>" required>
                      <div class="invalid-feedback">Please enter a valid province.</div>
                    </div>

                    <div class="col-12">
                      <label for="regCity" class="form-label">City / Municipality</label>
                      <input type="text" name="regCity" class="login-form" id="regCity"
                             value="<?= htmlspecialchars($_POST['regCity'] ?? '') ?>" required>
                      <div class="invalid-feedback">Please enter a valid city/municipality.</div>
                    </div>

                    <div class="col-12"><br>
                      <button class="flexible-wide-button" name="register" type="submit">Register</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Already have an account? <a href="index.php">Sign in</a></p>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a></div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="include/styles/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="include/styles/main.js"></script>
</body>
</html>
