<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once './include/connect/dbcon.php';

$message = '';
$messageType = '';

// Show success message after registration
if (isset($_GET['registered'])) {
    $message = 'Account created successfully! Please log in.';
    $messageType = 'success';
}

try {
    $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST["login"])) {

        if (empty($_POST["UserName"]) || empty($_POST["PassWord"])) {
            $message = 'All fields are required.';
            $messageType = 'danger';
        } else {

            $pdoQuery = "SELECT * FROM tbuser WHERE UserName = :UserName";
            $pdoResult = $pdoConnect->prepare($pdoQuery);
            $pdoResult->execute(['UserName' => $_POST["UserName"]]);
            $user = $pdoResult->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($_POST["PassWord"], $user["PassWord"])) {

                $_SESSION["UserName"]    = $user["UserName"];
                $_SESSION["role_id"]     = $user["role_id"];
                $_SESSION["id"]          = $user["id"];
                $_SESSION["cm_id"]       = $user["cm_id"];
                $_SESSION["province_id"] = $user["province_id"];

                // Audit trail
                $pdoQuery  = "INSERT INTO audit_trail (action, user) VALUES ('User logged in', :user)";
                $pdoResult = $pdoConnect->prepare($pdoQuery);
                $pdoResult->execute(['user' => $_SESSION["UserName"]]);

                switch ($user["role_id"]) {
                    case 1:
                        header("Location: ./page/users/farmers/home.php");
                        break;
                    case 2:
                        header("Location: ./page/users/admin/home.php");
                        break;
                    case 3:
                        header("Location: ./page/users/superadmin/home.php");
                        break;
                    default:
                        $message = 'Invalid role assigned to this account.';
                        $messageType = 'danger';
                }
                exit;

            } else {
                $message = 'Incorrect username or password.';
                $messageType = 'danger';
            }
        }
    }

} catch (PDOException $error) {
    $message = $error->getMessage();
    $messageType = 'danger';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Farm Management System</title>
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
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Sign in to your account.</p>
                  </div>

                  <form class="row g-3 needs-validation" method="POST" novalidate>
                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <input type="text" name="UserName" class="login-form" id="yourUsername" required>
                        <div class="invalid-feedback">Enter your username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="PassWord" class="login-form" id="yourPassword" required>
                      <div class="invalid-feedback">Enter your password.</div>
                    </div>

                    <div class="col-12"><br>
                      <button class="flexible-wide-button" name="login" value="login" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Don't have an account? <a href="register.php">Sign Up</a></p>
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
