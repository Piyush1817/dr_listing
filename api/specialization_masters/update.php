<?php

require_once "../../includes/cors.php";
require_once "../../includes/admin_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$id = $_POST['id'] ?? '';

$name = trim($_POST['name'] ?? '');

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($id) || empty($name)) {

    error("ID and name are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK SPECIALIZATION EXISTS
|--------------------------------------------------------------------------
*/

$specializationQuery = "
SELECT id
FROM specialization_masters
WHERE id = ?
AND status = 1
LIMIT 1
";

$specializationStmt = $conn->prepare($specializationQuery);

$specializationStmt->execute([$id]);

$specialization = $specializationStmt->fetch(PDO::FETCH_ASSOC);

if(!$specialization) {

    error("Specialization not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK DUPLICATE NAME
|--------------------------------------------------------------------------
*/

$duplicateQuery = "
SELECT id
FROM specialization_masters
WHERE LOWER(name) = LOWER(?)
AND id != ?
AND status = 1
LIMIT 1
";

$duplicateStmt = $conn->prepare($duplicateQuery);

$duplicateStmt->execute([

    $name,
    $id
]);

$duplicate = $duplicateStmt->fetch(PDO::FETCH_ASSOC);

if($duplicate) {

    error("Specialization already exists");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE SPECIALIZATION
|--------------------------------------------------------------------------
*/

$updateQuery = "
UPDATE specialization_masters
SET
    name = ?,
    updated_at = NOW()
WHERE id = ?
";

$updateStmt = $conn->prepare($updateQuery);

$isUpdated = $updateStmt->execute([

    $name,
    $id
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isUpdated) {

    success("Specialization updated successfully");

} else {

    error("Update failed");
}