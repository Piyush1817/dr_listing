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

$name = trim($_POST['name'] ?? '');

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($name)) {

    error("Specialization name is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK EXISTING SPECIALIZATION
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id, status
FROM specialization_masters
WHERE LOWER(name) = LOWER(?)
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$name]);

$specialization = $checkStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| ALREADY EXISTS
|--------------------------------------------------------------------------
*/

if($specialization && $specialization['status'] == 1) {

    error("Specialization already exists");
    exit;
}

/*
|--------------------------------------------------------------------------
| RESTORE OLD RECORD
|--------------------------------------------------------------------------
*/

if($specialization && $specialization['status'] == 0) {

    $restoreQuery = "
    UPDATE specialization_masters
    SET
        status = 1,
        updated_at = NOW()
    WHERE id = ?
    ";

    $restoreStmt = $conn->prepare($restoreQuery);

    $isRestored = $restoreStmt->execute([

        $specialization['id']
    ]);

    if($isRestored) {

        success("Specialization restored successfully");

    } else {

        error("Restore failed");
    }

    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT SPECIALIZATION
|--------------------------------------------------------------------------
*/

$insertQuery = "
INSERT INTO specialization_masters
(
    name,
    status,
    created_at
)
VALUES
(
    ?, ?, NOW()
)
";

$insertStmt = $conn->prepare($insertQuery);

$isInserted = $insertStmt->execute([

    $name,
    1
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isInserted) {

    success("Specialization created successfully");

} else {

    error("Specialization creation failed");
}