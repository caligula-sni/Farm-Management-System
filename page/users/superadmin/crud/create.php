<?php

    //pdo insert data to mysql database password hashing
    if (isset ($_POST['insert'])) {
        try {

            // connect to mysql
            $pdoConnect = new PDO("mysql:host=localhost;dbname=dbfarm2", "root", "");

            // set the PDO error mode to exception
            $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Start the transaction
            $pdoConnect->beginTransaction();

        // get values from input text and number 
        $User = $_POST['User'];
        $Pass = password_hash($_POST['Pass'], PASSWORD_DEFAULT);
        $Fname = $_POST['FName'];
        $Role = $_POST['Role'];
        $Province = ucwords(strtolower(trim($_POST['Province'])));
        $CityMuni = ucwords(strtolower(trim($_POST['CityMuni'])));

        // Check if province exists, if not insert
        $pdoQueryProvince = "SELECT province_id FROM tbprovince WHERE province_name = :province_name";
        $pdoResultProvince = $pdoConnect->prepare($pdoQueryProvince);
        $pdoResultProvince->execute([':province_name' => $Province]);
        $provinceRow = $pdoResultProvince->fetch(PDO::FETCH_ASSOC);

        if ($provinceRow) {
            $provinceID = $provinceRow['province_id'];
        } else {
            $pdoInsertProvince = "INSERT INTO tbprovince (province_name) VALUES (:province_name)";
            $pdoResultInsertProvince = $pdoConnect->prepare($pdoInsertProvince);
            $pdoResultInsertProvince->execute([':province_name' => $Province]);
            $provinceID = $pdoConnect->lastInsertId();
        }

        // Check if city/municipality exists, if not insert
        $pdoQueryCity = "SELECT cm_id FROM tbcitymuni WHERE cm_name = :cm_name AND province_id = :province_id";
        $pdoResultCity = $pdoConnect->prepare($pdoQueryCity);
        $pdoResultCity->execute([
            ':cm_name' => $CityMuni,
            ':province_id' => $provinceID
        ]);
        $cityRow = $pdoResultCity->fetch(PDO::FETCH_ASSOC);

        if ($cityRow) {
            $cityID = $cityRow['cm_id'];
        } else {
            // Insert new city/municipality 
            $pdoInsertCity = "INSERT INTO tbcitymuni (cm_name, province_id) VALUES (:cm_name, :province_id)";
            $pdoResultInsertCity = $pdoConnect->prepare($pdoInsertCity);
            $pdoResultInsertCity->execute([
                ':cm_name' => $CityMuni,
                ':province_id' => $provinceID
            ]);
            $cityID = $pdoConnect->lastInsertId();
        }

        // mysql query to insert data with hashed password
        $pdoQuery = "INSERT INTO tbuser (UserName, PassWord, FullName, role_id, province_id, cm_id)
         VALUES (:User, :Pass, :FName, :Role, :provinceID, :cityID)";
        $pdoResult = $pdoConnect->prepare($pdoQuery);
        $pdoExec = $pdoResult->execute(array(":User" => $User, ":Pass" => $Pass, ":FName" => $Fname, ":Role" => $Role, ":provinceID" => $provinceID, ":cityID" => $cityID ));

        //check if mysql insert query successful
        if ($pdoExec) {

            // Log the action in the audit trail 
            $pdoQuery = "INSERT INTO audit_trail (action) VALUES ('User Created')";
            $pdoResult = $pdoConnect->prepare($pdoQuery);
            $pdoResult->execute();

            // Commit the transaction
            $pdoConnect->commit();
            while ($row = $pdoResult->fetch()) {
                echo $row['id'] . " | " . $row['UserName'] . " | " . $row['PassWord'] . " | " . $row['FullName'] . " | " . $row['role_id'] . " | " . $row['province_id'] . " | " . $row['cm_id'] . "<br/>";
            }
            header("Location: read.php");
            exit;
        } else {
            // Roll back if anything fails
            $pdoConnect->rollBack();
            echo "Data not Inserted";
        }
    } 
         catch (PDOException $exc) {
            //Roll back in case of any exception
            $pdoConnect->rollBack();
            echo $exc->getMessage();
            exit();
    }
    }
    $pdoConnect = null;
?>

