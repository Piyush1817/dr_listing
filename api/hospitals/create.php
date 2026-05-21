<?php

require_once "../../includes/cors.php";
require_once "../../includes/doctor_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$name = $_POST['name'] ?? '';
$hospital_type = $_POST['hospital_type'] ?? '';
$phone = $_POST['phone'] ?? '';
$description = $_POST['description'] ?? '';

$address_line1 = $_POST['address_line1'] ?? '';
$address_line2 = $_POST['address_line2'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$country = $_POST['country'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$latitude = $_POST['latitude'] ?? '';
$longitude = $_POST['longitude'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(
    empty($name) ||
    empty($hospital_type) ||
    empty($phone)
) {

    error("Name, hospital type and phone are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK DUPLICATE HOSPITAL
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM hospitals
WHERE name = ?
AND phone = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([

    $name,
    $phone
]);

$hospitalExists = $checkStmt->fetch(PDO::FETCH_ASSOC);

if($hospitalExists) {

    error("Hospital already exists");
    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT HOSPITAL
|--------------------------------------------------------------------------
*/

$hospitalQuery = "
INSERT INTO hospitals
(
    name,
    hospital_type,
    phone,
    description,
    status,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, NOW()
)
";

$hospitalStmt = $conn->prepare($hospitalQuery);

$isHospitalInserted = $hospitalStmt->execute([

    $name,
    $hospital_type,
    $phone,
    $description,
    1
]);

if(!$isHospitalInserted) {

    error("Hospital creation failed");
    exit;
}

/*
|--------------------------------------------------------------------------
| GET LAST INSERTED HOSPITAL ID
|--------------------------------------------------------------------------
*/

$hospitalId = $conn->lastInsertId();

/*
|--------------------------------------------------------------------------
| INSERT HOSPITAL ADDRESS
|--------------------------------------------------------------------------
*/

$addressQuery = "
INSERT INTO hospital_addresses
(
    hospital_id,
    addresses_line1,
    addresses_line2,
    city,
    state,
    country,
    pincode,
    latitude,
    longitude,
    status,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
)
";

$addressStmt = $conn->prepare($addressQuery);

$isAddressInserted = $addressStmt->execute([

    $hospitalId,
    $address_line1,
    $address_line2,
    $city,
    $state,
    $country,
    $pincode,
    $latitude,
    $longitude,
    1
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isAddressInserted) {

    success("Hospital created successfully");

} else {

    error("Hospital address creation failed");
}