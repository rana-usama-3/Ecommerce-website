<?php
 $host = 'localhost';
 $dbname = 'dress_shop';
 $username = 'root';
 $password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Safe Session Start (Ye check karega ke session pehle se start to nahi hai)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>