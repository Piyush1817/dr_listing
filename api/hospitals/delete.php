<?php

require_once "../../includes/cors.php";
require_once "../../includes/doctor_auth.php";
require_once "../../includes/admin_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET HOSPITAL ID
|--------------------------------------------------------------------------
*/

$id = $_POST['id'] ?? '';

if(empty($id)) {

    error("Hospital ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK HOSPITAL EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM hospitals
WHERE id = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$id]);

$hospital = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$hospital) {

    error("Hospital not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| SOFT DELETE HOSPITAL
|--------------------------------------------------------------------------
*/

$query = "
UPDATE hospitals
SET
    status = 0,
    updated_at = NOW()
WHERE id = ?
";

$stmt = $conn->prepare($query);

$isDeleted = $stmt->execute([$id]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isDeleted) {

    success("Hospital deleted successfully");

} else {

    error("Delete failed");
}