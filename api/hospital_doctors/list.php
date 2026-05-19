<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET DOCTOR ID
|--------------------------------------------------------------------------
*/

$doctor_id = $_GET['doctor_id'] ?? '';

if(empty($doctor_id)) {

    error("Doctor ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| FETCH HOSPITALS OF DOCTOR
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    h.id,
    h.name,
    h.hospital_type,
    h.phone,
    h.description,

    ha.city,
    ha.state,
    ha.country,
    ha.latitude,
    ha.longitude

FROM hospital_doctors hd

INNER JOIN hospitals h
ON hd.hospital_id = h.id

LEFT JOIN hospital_addresses ha
ON h.id = ha.hospital_id

WHERE
    hd.doctor_id = ?
AND
    hd.status = 1
AND
    h.status = 1

ORDER BY h.id DESC
";

$stmt = $conn->prepare($query);

$stmt->execute([$doctor_id]);

$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Hospitals fetched successfully", $hospitals);