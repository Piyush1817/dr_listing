<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET SCHEDULE ID
|--------------------------------------------------------------------------
*/

$id = $_POST['id'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($id)) {

    error("Schedule ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK SCHEDULE EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM hospital_doctor_schedules
WHERE id = ?
AND status = 1
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$id]);

$schedule = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$schedule) {

    error("Schedule not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| DELETE SCHEDULE
|--------------------------------------------------------------------------
*/

$deleteQuery = "
UPDATE hospital_doctor_schedules
SET
    status = 0,
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

    success("Schedule deleted successfully");

} else {

    error("Schedule deletion failed");
}