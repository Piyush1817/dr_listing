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

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($id)) {

    error("ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK SPECIALIZATION EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM specialization_masters
WHERE id = ?
AND status = 1
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$id]);

$specialization = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$specialization) {

    error("Specialization not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| SOFT DELETE
|--------------------------------------------------------------------------
*/

$removeQuery = "
UPDATE specialization_masters
SET
    status = 0,
    updated_at = NOW()
WHERE id = ?
";

$removeStmt = $conn->prepare($removeQuery);

$isRemoved = $removeStmt->execute([

    $id
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isRemoved) {

    success("Specialization removed successfully");

} else {

    error("Removal failed");
}