<?php

require_once "../../includes/cors.php";
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
| CHECK DEGREE EXISTS
|--------------------------------------------------------------------------
*/

$degreeQuery = "
SELECT id
FROM degree_masters
WHERE id = ?
AND status = 1
LIMIT 1
";

$degreeStmt = $conn->prepare($degreeQuery);

$degreeStmt->execute([$id]);

$degree = $degreeStmt->fetch(PDO::FETCH_ASSOC);

if(!$degree) {

    error("Degree not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK DUPLICATE NAME
|--------------------------------------------------------------------------
*/

$duplicateQuery = "
SELECT id
FROM degree_masters
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

    error("Degree already exists");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE DEGREE
|--------------------------------------------------------------------------
*/

$updateQuery = "
UPDATE degree_masters
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

    success("Degree updated successfully");

} else {

    error("Update failed");
}