<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(empty($email) || empty($password)) {

    error("Email and password required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK ADMIN
|--------------------------------------------------------------------------
*/

$adminQuery = "SELECT * FROM admins WHERE email = ? LIMIT 1";

$adminStmt = $conn->prepare($adminQuery);

$adminStmt->execute([$email]);

$admin = $adminStmt->fetch(PDO::FETCH_ASSOC);

if($admin && password_verify($password, $admin['password'])) {

    success("Admin login successful", [

        "id" => $admin['id'],
        "name" => $admin['name'],
        "email" => $admin['email'],
        "role" => "admin"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK DOCTOR
|--------------------------------------------------------------------------
*/

$doctorQuery = "SELECT * FROM doctors WHERE email = ? AND status = 1 LIMIT 1";

$doctorStmt = $conn->prepare($doctorQuery);

$doctorStmt->execute([$email]);

$doctor = $doctorStmt->fetch(PDO::FETCH_ASSOC);

if($doctor && password_verify($password, $doctor['password'])) {

    success("Doctor login successful", [

        "id" => $doctor['id'],
        "name" => $doctor['name'],
        "email" => $doctor['email'],
        "role" => "doctor"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| INVALID LOGIN
|--------------------------------------------------------------------------
*/

error("Invalid credentials");