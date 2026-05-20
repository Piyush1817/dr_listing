<?php


require_once "../../includes/cors.php";
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

    error("Degree name is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK EXISTING DEGREE
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id, status
FROM degree_masters
WHERE LOWER(name) = LOWER(?)
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$name]);

$degree = $checkStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| ALREADY EXISTS
|--------------------------------------------------------------------------
*/

if($degree && $degree['status'] == 1) {

    error("Degree already exists");
    exit;
}

/*
|--------------------------------------------------------------------------
| RESTORE OLD RECORD
|--------------------------------------------------------------------------
*/

if($degree && $degree['status'] == 0) {

    $restoreQuery = "
    UPDATE degree_masters
    SET
        status = 1,
        updated_at = NOW()
    WHERE id = ?
    ";

    $restoreStmt = $conn->prepare($restoreQuery);

    $isRestored = $restoreStmt->execute([

        $degree['id']
    ]);

    if($isRestored) {

        success("Degree restored successfully");

    } else {

        error("Restore failed");
    }

    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT DEGREE
|--------------------------------------------------------------------------
*/

$insertQuery = "
INSERT INTO degree_masters
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

    success("Degree created successfully");

} else {

    error("Degree creation failed");
}