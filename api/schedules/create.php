<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$hospital_doctor_id = $_POST['hospital_doctor_id'] ?? '';
$day_of_week = $_POST['day_of_week'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(
    empty($hospital_doctor_id) ||
    empty($day_of_week) ||
    empty($start_time) ||
    empty($end_time)
) {

    error("All fields are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK HOSPITAL DOCTOR MAPPING EXISTS
|--------------------------------------------------------------------------
*/

$mappingQuery = "
SELECT id
FROM hospital_doctors
WHERE id = ?
AND status = 1
LIMIT 1
";

$mappingStmt = $conn->prepare($mappingQuery);

$mappingStmt->execute([$hospital_doctor_id]);

$mapping = $mappingStmt->fetch(PDO::FETCH_ASSOC);

if(!$mapping) {

    error("Hospital doctor mapping not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK EXISTING SCHEDULE
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id, status
FROM hospital_doctor_schedules
WHERE hospital_doctor_id = ?
AND day_of_week = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([

    $hospital_doctor_id,
    $day_of_week
]);

$schedule = $checkStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| IF SCHEDULE ALREADY ACTIVE
|--------------------------------------------------------------------------
*/

if($schedule && $schedule['status'] == 1) {

    error("Schedule already exists for this day");
    exit;
}

/*
|--------------------------------------------------------------------------
| REACTIVATE OLD SCHEDULE
|--------------------------------------------------------------------------
*/

if($schedule && $schedule['status'] == 0) {

    $reactivateQuery = "
    UPDATE hospital_doctor_schedules
    SET
        status = 1,
        start_time = ?,
        end_time = ?,
        updated_at = NOW()
    WHERE id = ?
    ";

    $reactivateStmt = $conn->prepare($reactivateQuery);

    $isReactivated = $reactivateStmt->execute([

        $start_time,
        $end_time,
        $schedule['id']
    ]);

    if($isReactivated) {

        success("Schedule restored successfully");

    } else {

        error("Schedule restore failed");
    }

    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT NEW SCHEDULE
|--------------------------------------------------------------------------
*/

$insertQuery = "
INSERT INTO hospital_doctor_schedules
(
    hospital_doctor_id,
    day_of_week,
    start_time,
    end_time,
    status,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, NOW()
)
";

$insertStmt = $conn->prepare($insertQuery);

$isInserted = $insertStmt->execute([

    $hospital_doctor_id,
    $day_of_week,
    $start_time,
    $end_time,
    1
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isInserted) {

    success("Schedule created successfully");

} else {

    error("Schedule creation failed");
}