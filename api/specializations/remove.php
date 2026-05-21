<?php

require_once "../../includes/cors.php";
require_once "../../includes/doctor_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

$doctor_id = $_SESSION['doctor_id'];

$specialization_id = $_POST['specialization_id'] ?? '';

if(empty($specialization_id)) {

    error("Specialization ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK MAPPING EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM doctor_specializations
WHERE doctor_id = ?
AND specialization_id = ?
AND status = 1
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([

    $doctor_id,
    $specialization_id
]);

$mapping = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$mapping) {

    error("Specialization mapping not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| REMOVE SPECIALIZATION
|--------------------------------------------------------------------------
*/

$removeQuery = "
UPDATE doctor_specializations
SET
    status = 0,
    updated_at = NOW()
WHERE doctor_id = ?
AND specialization_id = ?
";

$removeStmt = $conn->prepare($removeQuery);

$isRemoved = $removeStmt->execute([

    $doctor_id,
    $specialization_id
]);

if($isRemoved) {

    success("Specialization removed successfully");

} else {

    error("Remove failed");
}