<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET HOSPITAL DOCTOR ID
|--------------------------------------------------------------------------
*/

$hospital_doctor_id = $_GET['hospital_doctor_id'] ?? '';

if(empty($hospital_doctor_id)) {

    error("Hospital Doctor ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| FETCH SCHEDULES
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    id,
    day_of_week,
    start_time,
    end_time

FROM hospital_doctor_schedules

WHERE
    hospital_doctor_id = ?
AND
    status = 1

ORDER BY id ASC
";

$stmt = $conn->prepare($query);

$stmt->execute([$hospital_doctor_id]);

$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Schedules fetched successfully", $schedules);