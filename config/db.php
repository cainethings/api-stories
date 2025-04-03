<?php

$host = "srv1554.hstgr.io";   // Change if your DB is on another server
$user = "u625450350_compass_admin"; // Replace with your MySQL username
$pass = "adminPass@43!"; // Replace with your MySQL password
$dbname = "u625450350_compass"; // Your database name

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "Database connection failed: " . $e->getMessage()]));
}