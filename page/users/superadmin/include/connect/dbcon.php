<?php
try {
    // Update credentials below to match your MySQL setup
    $pdoConnect = new PDO("mysql:host=localhost;dbname=dbfarm2", "root", "");
    $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exc) {
    die("Database connection failed: " . $exc->getMessage());
}
?>
