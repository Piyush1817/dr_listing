<?php

require_once "../../includes/cors.php";
require_once "../../includes/doctor_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

$doctor_id = $_SESSION['doctor_id'];

$id = $_POST['id'] ?? '';

$day_of_week = $_POST['day_of_week'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';

if(empty($id)) {

    error("Schedule ID is required");
    exit;
}

if(
    empty($day_of_week) ||
    empty($start_time) ||
    empty($end_time)
) {

    error("All fields are required");
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
AND doctor_id = ?
AND status = 1
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([

    $id,
    $doctor_id
]);

$schedule = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$schedule) {

    error("Schedule not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE SCHEDULE
|--------------------------------------------------------------------------
*/

$updateQuery = "
UPDATE hospital_doctor_schedules
SET
    day_of_week = ?,
    start_time = ?,
    end_time = ?,
    updated_at = NOW()
WHERE id = ?
AND doctor_id = ?
";

$updateStmt = $conn->prepare($updateQuery);

$isUpdated = $updateStmt->execute([

    $day_of_week,
    $start_time,
    $end_time,
    $id,
    $doctor_id
]);

if($isUpdated) {

    success("Schedule updated successfully");

} else {

    error("Schedule update failed");
}