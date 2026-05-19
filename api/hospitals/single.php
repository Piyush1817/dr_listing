<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET HOSPITAL ID
|--------------------------------------------------------------------------
*/

$id = $_GET['id'] ?? '';

if(empty($id)) {

    error("Hospital ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| FETCH SINGLE HOSPITAL
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    h.id,
    h.name,
    h.hospital_type,
    h.phone,
    h.description,

    ha.addresses_line1,
    ha.addresses_line2,
    ha.city,
    ha.state,
    ha.country,
    ha.pincode,
    ha.latitude,
    ha.longitude

FROM hospitals h

LEFT JOIN hospital_addresses ha
ON h.id = ha.hospital_id

WHERE
    h.id = ?
AND
    h.status = 1

LIMIT 1
";

$stmt = $conn->prepare($query);

$stmt->execute([$id]);

$hospital = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| CHECK HOSPITAL EXISTS
|--------------------------------------------------------------------------
*/

if(!$hospital) {

    error("Hospital not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Hospital fetched successfully", $hospital);