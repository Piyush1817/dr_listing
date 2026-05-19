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
$hospital_id = $_POST['hospital_id'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($doctor_id) || empty($hospital_id)) {

    error("Doctor ID and Hospital ID are required");
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

/*
|--------------------------------------------------------------------------
| CHECK HOSPITAL EXISTS
|--------------------------------------------------------------------------
*/

$hospitalQuery = "
SELECT id
FROM hospitals
WHERE id = ?
AND status = 1
LIMIT 1
";

$hospitalStmt = $conn->prepare($hospitalQuery);

$hospitalStmt->execute([$hospital_id]);

$hospital = $hospitalStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| VALIDATE BOTH
|--------------------------------------------------------------------------
*/

if(!$doctor && !$hospital) {

    error("Doctor and Hospital not found");
    exit;
}

if(!$doctor) {

    error("Doctor not found");
    exit;
}

if(!$hospital) {

    error("Hospital not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK EXISTING MAPPING
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id, status
FROM hospital_doctors
WHERE doctor_id = ?
AND hospital_id = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([

    $doctor_id,
    $hospital_id
]);

$mapping = $checkStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| IF ALREADY ACTIVE
|--------------------------------------------------------------------------
*/

if($mapping && $mapping['status'] == 1) {

    error("Doctor already assigned to this hospital");
    exit;
}

/*
|--------------------------------------------------------------------------
| REACTIVATE OLD MAPPING
|--------------------------------------------------------------------------
*/

if($mapping && $mapping['status'] == 0) {

    $reactivateQuery = "
    UPDATE hospital_doctors
    SET
        status = 1,
        updated_at = NOW()
    WHERE id = ?
    ";

    $reactivateStmt = $conn->prepare($reactivateQuery);

    $isReactivated = $reactivateStmt->execute([

        $mapping['id']
    ]);

    if($isReactivated) {

        success("Doctor reassigned successfully");
    
    } else {

        error("Reassignment failed");
    }

    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT NEW MAPPING
|--------------------------------------------------------------------------
*/

$insertQuery = "
INSERT INTO hospital_doctors
(
    doctor_id,
    hospital_id,
    status,
    created_at
)
VALUES
(
    ?, ?, ?, NOW()
)
";

$insertStmt = $conn->prepare($insertQuery);

$isInserted = $insertStmt->execute([

    $doctor_id,
    $hospital_id,
    1
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isInserted) {

    success("Doctor assigned successfully");

} else {

    error("Assignment failed");
}