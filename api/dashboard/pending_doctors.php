<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| FETCH PENDING DOCTORS
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    id,
    name,
    email,
    phone,
    profile_image,
    qualification,
    consulting_fee,
    availability_status,
    average_status,
    created_at

FROM doctors

WHERE status = 0

ORDER BY created_at DESC
";

$stmt = $conn->prepare($query);

$stmt->execute();

$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Pending doctors fetched successfully", $doctors);