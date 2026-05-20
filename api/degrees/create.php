<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$doctor_id = $_POST['doctor_id'] ?? '';
$degree_id = $_POST['degree_id'] ?? '';
$institute_name = $_POST['institute_name'] ?? '';
$year_of_passing = $_POST['year_of_passing'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(
    empty($doctor_id) ||
    empty($degree_id) ||
    empty($institute_name) ||
    empty($year_of_passing)
) {

    error("All fields are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK DOCTOR EXISTS
|--------------------------------------------------------------------------
*/

$doctorQuery = "
SELECT id
FROM doctors
WHERE id = ?
AND status = 1
LIMIT 1
";

$doctorStmt = $conn->prepare($doctorQuery);

$doctorStmt->execute([$doctor_id]);

$doctor = $doctorStmt->fetch(PDO::FETCH_ASSOC);

if(!$doctor) {

    error("Doctor not found");
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

$degreeStmt->execute([$degree_id]);

$degree = $degreeStmt->fetch(PDO::FETCH_ASSOC);

if(!$degree) {

    error("Degree not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK EXISTING DEGREE
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id, status
FROM degrees
WHERE doctor_id = ?
AND degree_id = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([

    $doctor_id,
    $degree_id
]);

$existingDegree = $checkStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| DEGREE ALREADY ACTIVE
|--------------------------------------------------------------------------
*/

if($existingDegree && $existingDegree['status'] == 1) {

    error("Degree already assigned");
    exit;
}

/*
|--------------------------------------------------------------------------
| RESTORE OLD DEGREE
|--------------------------------------------------------------------------
*/

if($existingDegree && $existingDegree['status'] == 0) {

    $restoreQuery = "
    UPDATE degrees
    SET
        status = 1,
        institute_name = ?,
        year_of_passing = ?,
        updated_at = NOW()
    WHERE id = ?
    ";

    $restoreStmt = $conn->prepare($restoreQuery);

    $isRestored = $restoreStmt->execute([

        $institute_name,
        $year_of_passing,
        $existingDegree['id']
    ]);

    if($isRestored) {

        success("Degree restored successfully");

    } else {

        error("Degree restore failed");
    }

    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT DEGREE
|--------------------------------------------------------------------------
*/

$insertQuery = "
INSERT INTO degrees
(
    doctor_id,
    degree_id,
    institute_name,
    year_of_passing,
    status,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, NOW()
)
";

$insertStmt = $conn->prepare($insertQuery);

$isInserted = $insertStmt->execute([

    $doctor_id,
    $degree_id,
    $institute_name,
    $year_of_passing,
    1
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isInserted) {

    success("Degree assigned successfully");

} else {

    error("Degree assignment failed");
}