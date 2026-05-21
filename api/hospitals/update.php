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

$id = $_POST['id'] ?? '';

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

if(empty($id)) {

    error("Hospital ID is required");
    exit;
}

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
| CHECK HOSPITAL EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM hospitals
WHERE id = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$id]);

$hospitalExists = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$hospitalExists) {

    error("Hospital not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE HOSPITAL
|--------------------------------------------------------------------------
*/

$hospitalQuery = "
UPDATE hospitals
SET
    name = ?,
    hospital_type = ?,
    phone = ?,
    description = ?,
    updated_at = NOW()
WHERE id = ?
";

$hospitalStmt = $conn->prepare($hospitalQuery);

$isHospitalUpdated = $hospitalStmt->execute([

    $name,
    $hospital_type,
    $phone,
    $description,
    $id
]);

/*
|--------------------------------------------------------------------------
| UPDATE HOSPITAL ADDRESS
|--------------------------------------------------------------------------
*/

$addressQuery = "
UPDATE hospital_addresses
SET
    addresses_line1 = ?,
    addresses_line2 = ?,
    city = ?,
    state = ?,
    country = ?,
    pincode = ?,
    latitude = ?,
    longitude = ?,
    updated_at = NOW()
WHERE hospital_id = ?
";

$addressStmt = $conn->prepare($addressQuery);

$isAddressUpdated = $addressStmt->execute([

    $address_line1,
    $address_line2,
    $city,
    $state,
    $country,
    $pincode,
    $latitude,
    $longitude,
    $id
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isHospitalUpdated || $isAddressUpdated) {

    success("Hospital updated successfully");

} else {

    error("Update failed");
}