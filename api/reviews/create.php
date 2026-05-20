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
$patient_name = $_POST['patient_name'] ?? '';
$patient_email = $_POST['patient_email'] ?? '';
$review_title = $_POST['review_title'] ?? '';
$rating = $_POST['rating'] ?? '';
$review_text = $_POST['review_text'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(
    empty($doctor_id) ||
    empty($patient_name) ||
    empty($patient_email) ||
    empty($review_title) ||
    empty($rating) ||
    empty($review_text)
) {

    error("All fields are required");
    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDATE RATING
|--------------------------------------------------------------------------
*/

if($rating < 1 || $rating > 5) {

    error("Rating must be between 1 and 5");
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
| INSERT REVIEW
|--------------------------------------------------------------------------
*/

$insertQuery = "
INSERT INTO reviews
(
    doctor_id,
    patient_name,
    patient_email,
    review_title,
    rating,
    review_text,
    is_approved,
    status,
    created_at
)
VALUES
(
    ?, ?, ?, ?, ?, ?, ?, ?, NOW()
)
";

$insertStmt = $conn->prepare($insertQuery);

$isInserted = $insertStmt->execute([

    $doctor_id,
    $patient_name,
    $patient_email,
    $review_title,
    $rating,
    $review_text,
    0,
    1
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isInserted) {

    success("Review submitted successfully. Waiting for admin approval.");

} else {

    error("Review submission failed");
}