<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET DOCTOR ID
|--------------------------------------------------------------------------
*/

$id = $_GET['id'] ?? '';

if(empty($id)) {

    error("Doctor ID is required");
}

/*
|--------------------------------------------------------------------------
| GET SINGLE DOCTOR
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
WHERE id = ?
AND status = 1
LIMIT 1
";

$stmt = $conn->prepare($query);

$stmt->execute([$id]);

$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| CHECK DOCTOR EXISTS
|--------------------------------------------------------------------------
*/

if(!$doctor) {

    error("Doctor not found");
}

/*
|--------------------------------------------------------------------------
| ADD PROFILE IMAGE URL
|--------------------------------------------------------------------------
*/

$doctor['profile_image_url'] =

!empty($doctor['profile_image'])

? "http://localhost/dr_listing/uploads/doctors/" . $doctor['profile_image']

: null;

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Doctor fetched successfully", $doctor);