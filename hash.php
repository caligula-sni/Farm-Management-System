<?php

    $password = "5up3r4dM1N";

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    echo "Hashed Password: " . $hashedPassword;

?>

