<?php

    session_start();
    require_once './include/connect/dbcon.php';

    // Log the Log out action in the audit trail with timestamp
    if (isset($_SESSION["UserName"])) {
        $loggedInUser = $_SESSION["UserName"];
        $pdoQuery = "INSERT INTO audit_trail (action, user) VALUES ('User Logged out', :user)";
        $pdoResult = $pdoConnect->prepare($pdoQuery);
        $pdoResult->execute([':user' => $loggedInUser]);
    }
    unset($_SESSION['user']); // Remove the argument from session_unset
    session_destroy();
    header("Location: /wfp/index.php");
?>