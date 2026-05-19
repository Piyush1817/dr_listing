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
    exit;
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
    exit;
}

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Doctor fetched successfully", $doctor);