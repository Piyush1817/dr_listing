<?php

require_once "../../includes/cors.php";
require_once "../../includes/admin_auth.php";
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

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(
    empty($name) ||
    empty($email) ||
    empty($password)
) {

    error("All fields are required");
}

/*
|--------------------------------------------------------------------------
| CHECK EMAIL EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM admins
WHERE email = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$email]);

$adminExists = $checkStmt->fetch(PDO::FETCH_ASSOC);

if($adminExists) {

    error("Email already exists");
}

/*
|--------------------------------------------------------------------------
| HASH PASSWORD
|--------------------------------------------------------------------------
*/

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/*
|--------------------------------------------------------------------------
| CREATE ADMIN
|--------------------------------------------------------------------------
*/

$insertQuery = "
INSERT INTO admins (

    name,
    email,
    password,
    created_at

) VALUES (

    ?, ?, ?, NOW()
)
";

$insertStmt = $conn->prepare($insertQuery);

$isCreated = $insertStmt->execute([

    $name,
    $email,
    $hashedPassword
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isCreated) {

    success("Admin created successfully");

} else {

    error("Admin creation failed");
}