<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET PENDING DOCTORS
|--------------------------------------------------------------------------
*/

$query = "
SELECT
    id,
    name,
    email,
    phone,
    qualification,
    consulting_fee,
    availability_status,
    created_at
FROM doctors
WHERE status = 0
ORDER BY id DESC
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