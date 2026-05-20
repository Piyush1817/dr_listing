<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$doctor_id = $_POST['doctor_id'] ?? '';
$degree_id = $_POST['degree_id'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($doctor_id) || empty($degree_id)) {

    error("Doctor ID and Degree ID are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK DEGREE MAPPING EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM degrees
WHERE doctor_id = ?
AND degree_id = ?
AND status = 1
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([

    $doctor_id,
    $degree_id
]);

$degree = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$degree) {

    error("Degree mapping not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| REMOVE DEGREE
|--------------------------------------------------------------------------
*/

$removeQuery = "
UPDATE degrees
SET
    status = 0,
    updated_at = NOW()
WHERE doctor_id = ?
AND degree_id = ?
";

$removeStmt = $conn->prepare($removeQuery);

$isRemoved = $removeStmt->execute([

    $doctor_id,
    $degree_id
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isRemoved) {

    success("Degree removed successfully");

} else {

    error("Degree remove failed");
}