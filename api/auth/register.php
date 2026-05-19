<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$phone = $_POST['phone'] ?? '';
$profile_image = !empty($_POST['profile_image']) 
                    ? $_POST['profile_image'] 
                    : null;
        $description = !empty($_POST['description']) 
                    ? $_POST['description'] 
                    : null;
        $qualification = $_POST['qualification'] ?? '';
$consulting_fee = $_POST['consulting_fee'] ?? '';
$availability_status = $_POST['availability_status'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(
    empty($name) ||
    empty($email) ||
    empty($password) ||
    empty($phone) ||
    empty($qualification)
) {

    error("Name, email, password, phone and qualification are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK EMAIL ALREADY EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "SELECT id FROM doctors WHERE email = ? LIMIT 1";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$email]);

$emailExists = $checkStmt->fetch(PDO::FETCH_ASSOC);

if($emailExists) {

    error("Email already registered");
    exit;
}

/*
|--------------------------------------------------------------------------
| HASH PASSWORD
|--------------------------------------------------------------------------
*/

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/*
|--------------------------------------------------------------------------
| INSERT DOCTOR
|--------------------------------------------------------------------------
*/

$insertQuery = "
INSERT INTO doctors
(
    name,
    email,
    password,
    phone,
    profile_image,
    description,
    qualification,
    consulting_fee,
    availability_status,
    status,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
)
";

$insertStmt = $conn->prepare($insertQuery);

$isInserted = $insertStmt->execute([
    $name,
    $email,
    $hashedPassword,
    $phone,
    $profile_image,
    $description,
    $qualification,
    $consulting_fee,
    $availability_status,
    0
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isInserted) {

    success("Doctor registered successfully");

} else {

    error("Registration failed");
}