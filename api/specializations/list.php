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
| FETCH SPECIALIZATIONS
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    ds.id,
    sm.id AS specialization_id,
    sm.name

FROM doctor_specializations ds

INNER JOIN specialization_masters sm
ON ds.specialization_id = sm.id

WHERE
    ds.doctor_id = ?
AND
    ds.status = 1
AND
    sm.status = 1

ORDER BY sm.name ASC
";

$stmt = $conn->prepare($query);

$stmt->execute([$doctor_id]);

$specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Specializations fetched successfully", $specializations);