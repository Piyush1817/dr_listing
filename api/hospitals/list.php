<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| FETCH ALL HOSPITALS
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

WHERE h.status = 1

ORDER BY h.id DESC
";

$stmt = $conn->prepare($query);

$stmt->execute();

$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Hospitals fetched successfully", $hospitals);