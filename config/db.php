<?php

$host = "localhost";
$port = "3308";
$dbname = "listing_dir";
$username = "root";
$password = "root";

try {

    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {

    die("DB Connection Failed: " . $e->getMessage());
}