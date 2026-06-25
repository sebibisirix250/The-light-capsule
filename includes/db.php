<?php

//DATABASE CONNECTION GATEWAY - PDO INITIALIZER 

require_once __DIR__ . '/config.php'; //PULLS DATABASE INFORMATIONS

try {

    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", //CONNECTION BRIDGE
        DB_USER,
        DB_PASS
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  //CLEAN DEBUGGING
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //CLEAN ARRAYS HANDING
} catch (PDOException $e) {

    die("Database connection failed: " . $e->getMessage()); //ERROR
}
