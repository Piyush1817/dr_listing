<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

echo "START<br>";

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$doctor_id = $_POST['doctor_id'] ?? '';
$specialization_id = $_POST['specialization_id'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($doctor_id) || empty($specialization_id)) {

    error("Doctor ID and Specialization ID are required");
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

$specializationStmt->execute([$specialization_id]);

$specialization = $specializationStmt->fetch(PDO::FETCH_ASSOC);

if(!$specialization) {

    error("Specialization not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK EXISTING MAPPING
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id, status
FROM doctor_specializations
WHERE doctor_id = ?
AND specialization_id = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([

    $doctor_id,
    $specialization_id
]);

$mapping = $checkStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| ALREADY ACTIVE
|--------------------------------------------------------------------------
*/

if($mapping && $mapping['status'] == 1) {

    error("Specialization already assigned");
    exit;
}

/*
|--------------------------------------------------------------------------
| REACTIVATE OLD MAPPING
|--------------------------------------------------------------------------
*/

if($mapping && $mapping['status'] == 0) {

    $reactivateQuery = "
    UPDATE doctor_specializations
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

        success("Specialization restored successfully");

    } else {

        error("Restore failed");
    }

    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT NEW MAPPING
|--------------------------------------------------------------------------
*/

$insertQuery = "
INSERT INTO doctor_specializations
(
    doctor_id,
    specialization_id,
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
    $specialization_id,
    1
]);
if(!$isInserted) {

    print_r($insertStmt->errorInfo());
    exit;
}


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isInserted) {

    success("Specialization assigned successfully");

} else {

    error("Assignment failed");
}