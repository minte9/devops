<?php
$host = 'db';
$db   = 'app_db';
$user = 'app_user';
$pass = 'app_pass';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );
    echo "Connected to MySQL successfully!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
