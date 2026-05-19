<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$profile_image = $_POST['profile_image'] ?? null;
$description = $_POST['description'] ?? '';
$qualification = $_POST['qualification'] ?? '';
$consulting_fee = $_POST['consulting_fee'] ?? '';
$availability_status = $_POST['availability_status'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($id)) {

    error("Doctor ID is required");
    exit;
}

if(empty($name) || empty($phone) || empty($qualification)) {

    error("Name, phone and qualification are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK DOCTOR EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id FROM doctors
WHERE id = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$id]);

$doctor = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$doctor) {

    error("Doctor not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE DOCTOR
|--------------------------------------------------------------------------
*/

$updateQuery = "
UPDATE doctors
SET
    name = ?,
    phone = ?,
    profile_image = ?,
    description = ?,
    qualification = ?,
    consulting_fee = ?,
    availability_status = ?,
    updated_at = NOW()
WHERE id = ?
";

$updateStmt = $conn->prepare($updateQuery);

$isUpdated = $updateStmt->execute([

    $name,
    $phone,
    $profile_image,
    $description,
    $qualification,
    $consulting_fee,
    $availability_status,
    $id
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isUpdated) {

    success("Doctor updated successfully");

} else {

    error("Update failed");
}