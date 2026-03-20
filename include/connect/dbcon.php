<?php

    try {

        //connct to mysql
        $pdoConnect = new PDO("mysql:host=localhost;dbname=dbfarm2", "user01", "2389");
        
        // set the PDO error mode to exception 
        $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $exc) {
        echo $exc->getMessage();
    }
?>