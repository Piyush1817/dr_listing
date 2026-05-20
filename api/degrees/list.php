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
| FETCH DEGREES
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    d.id,
    dm.id AS degree_id,
    dm.name AS degree_name,
    d.institute_name,
    d.year_of_passing

FROM degrees d

INNER JOIN degree_masters dm
ON d.degree_id = dm.id

WHERE
    d.doctor_id = ?
AND
    d.status = 1
AND
    dm.status = 1

ORDER BY d.year_of_passing DESC
";

$stmt = $conn->prepare($query);

$stmt->execute([$doctor_id]);

$degrees = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Degrees fetched successfully", $degrees);