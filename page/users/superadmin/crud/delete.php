<!DOCTYPE html>
<html>
<head>
    <title>Add and Display</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../include/styles/styles.css">
</head>
<body>
    <div class = "container">
        <h1>Welcome to CRUD OPERATIONS</h1>
        <p>This is the landing page for your project. You can customize it as needed.</p>
    </div>  
</body>
</html>

<?php

    require_once ("../include/connect/dbcon.php");

    // Check if id parameter is set
    if (isset($_GET['id'])) {

        // Use prepared statement
        $pdoQuery = "DELETE FROM tbuser WHERE id = :id";
        $pdoResult = $pdoConnect->prepare($pdoQuery);
        $pdoResult->execute(array(':id' => $_GET['id']));

        // Redirect to sampleadd.php after deletion
        header('location:read.php');
    } else {

        // Handle the case where id is not set
        echo "Invalid Request. Please provide a valid id.";
    }
    $pdoConnect = null;
?>