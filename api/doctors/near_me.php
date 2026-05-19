<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET USER LOCATION
|--------------------------------------------------------------------------
*/

$latitude = $_GET['latitude'] ?? '';
$longitude = $_GET['longitude'] ?? '';

if(empty($latitude) || empty($longitude)) {

    error("Latitude and longitude are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| FIND NEARBY DOCTORS
|--------------------------------------------------------------------------
*/

$query = "

SELECT
    d.id,
    d.name,
    d.email,
    d.phone,
    d.profile_image,
    d.qualification,
    d.consulting_fee,
    d.availability_status,

    h.name AS hospital_name,

    ha.city,
    ha.state,
    ha.country,

    (
        6371 * ACOS(
            COS(RADIANS(?))
            * COS(RADIANS(ha.latitude))
            * COS(RADIANS(ha.longitude) - RADIANS(?))
            + SIN(RADIANS(?))
            * SIN(RADIANS(ha.latitude))
        )
    ) AS distance

FROM doctors d

INNER JOIN hospital_doctors hd
ON d.id = hd.doctor_id

INNER JOIN hospitals h
ON hd.hospital_id = h.id

INNER JOIN hospital_addresses ha
ON h.id = ha.hospital_id

WHERE
    d.status = 1
AND
    ha.status = 1

ORDER BY distance ASC

LIMIT 20
";

$stmt = $conn->prepare($query);

$stmt->execute([

    $latitude,
    $longitude,
    $latitude
]);

$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Nearby doctors fetched successfully", $doctors);