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
$description = $_POST['description'] ?? '';
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
    empty($phone)
) {

    error("Name, email, password and phone are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK EMAIL EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id FROM doctors
WHERE email = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$email]);

$emailExists = $checkStmt->fetch(PDO::FETCH_ASSOC);

if($emailExists) {

    error("Email already exists");
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
    description,
    qualification,
    consulting_fee,
    availability_status,
    status,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
)
";

$insertStmt = $conn->prepare($insertQuery);

$isInserted = $insertStmt->execute([

    $name,
    $email,
    $hashedPassword,
    $phone,
    $description,
    $qualification,
    $consulting_fee,
    $availability_status,
    1
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isInserted) {

    success("Doctor created successfully");

} else {

    error("Doctor creation failed");
}