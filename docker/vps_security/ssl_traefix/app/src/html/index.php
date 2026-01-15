<?php

$versions = [];

// Apache
$apache = explode(" ", explode("/", $_SERVER['SERVER_SOFTWARE'])[1])[0];
array_push($versions, $apache);

// PHP
$php = phpversion();
array_push($versions, $php);

// MySQL
try {
    $pdo = new PDO("mysql:host=db;dbname=app_db;charset=utf8mb4", "app_user", "app_pass");
    $mysql = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    $versions[] = $mysql;
} catch (PDOException $e) {}

// Versions
echo join(" / ", $versions);
