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
| CHECK DEGREE EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM degree_masters
WHERE id = ?
AND status = 1
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$id]);

$degree = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$degree) {

    error("Degree not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| SOFT DELETE
|--------------------------------------------------------------------------
*/

$removeQuery = "
UPDATE degree_masters
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

    success("Degree removed successfully");

} else {

    error("Removal failed");
}