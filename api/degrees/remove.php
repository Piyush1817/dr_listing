<?php

require_once "../../includes/cors.php";
require_once "../../includes/doctor_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

$doctor_id = $_SESSION['doctor_id'];

$degree_id = $_POST['degree_id'] ?? '';

if(empty($degree_id)) {

    error("Degree ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK DEGREE EXISTS
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

if($isRemoved) {

    success("Degree removed successfully");

} else {

    error("Degree remove failed");
}