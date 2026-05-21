<?php

require_once "../../includes/cors.php";
require_once "../../includes/admin_auth.php";
require_once "../../includes/doctor_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET DOCTOR ID
|--------------------------------------------------------------------------
*/

$id = $_POST['id'] ?? '';

if(empty($id)) {

    error("Doctor ID is required");
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
| SOFT DELETE DOCTOR
|--------------------------------------------------------------------------
*/

$deleteQuery = "
UPDATE doctors
SET
    status = 2,
    updated_at = NOW()
WHERE id = ?
";

$deleteStmt = $conn->prepare($deleteQuery);

$isDeleted = $deleteStmt->execute([$id]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isDeleted) {

    success("Doctor deleted successfully");

} else {

    error("Delete failed");
}