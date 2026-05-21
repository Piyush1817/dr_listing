<?php

require_once "../../includes/cors.php";
require_once "../../includes/doctor_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

$doctor_id = $_SESSION['doctor_id'];

$hospital_id = $_POST['hospital_id'] ?? '';

if(empty($hospital_id)) {

    error("Hospital ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK MAPPING EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM hospital_doctors
WHERE doctor_id = ?
AND hospital_id = ?
AND status = 1
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([

    $doctor_id,
    $hospital_id
]);

$mapping = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$mapping) {

    error("Mapping not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| REMOVE MAPPING
|--------------------------------------------------------------------------
*/

$removeQuery = "
UPDATE hospital_doctors
SET
    status = 0,
    updated_at = NOW()
WHERE doctor_id = ?
AND hospital_id = ?
";

$removeStmt = $conn->prepare($removeQuery);

$isRemoved = $removeStmt->execute([

    $doctor_id,
    $hospital_id
]);

if($isRemoved) {

    success("Doctor removed from hospital successfully");

} else {

    error("Remove failed");
}