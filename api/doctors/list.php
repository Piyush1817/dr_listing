<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET APPROVED DOCTORS
|--------------------------------------------------------------------------
*/

$query = "
SELECT
    id,
    name,
    email,
    phone,
    profile_image,
    description,
    qualification,
    consulting_fee,
    availability_status,
    created_at
FROM doctors
WHERE status = 1
ORDER BY id DESC
";

$stmt = $conn->prepare($query);

$stmt->execute();

$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| ADD PROFILE IMAGE URL
|--------------------------------------------------------------------------
*/

foreach($doctors as &$doctor) {

    $doctor['profile_image_url'] =

    !empty($doctor['profile_image'])

    ? "http://localhost/dr_listing/uploads/doctors/" . $doctor['profile_image']

    : null;
}

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Doctors fetched successfully", $doctors);