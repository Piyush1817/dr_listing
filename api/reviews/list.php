<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET DOCTOR ID
|--------------------------------------------------------------------------
*/

$doctor_id = $_GET['doctor_id'] ?? '';

if(empty($doctor_id)) {

    error("Doctor ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| FETCH REVIEWS
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    id,
    patient_name,
    review_title,
    rating,
    review_text,
    created_at

FROM reviews

WHERE
    doctor_id = ?
AND
    is_approved = 1
AND
    status = 1

ORDER BY created_at DESC
";

$stmt = $conn->prepare($query);

$stmt->execute([$doctor_id]);

$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Reviews fetched successfully", $reviews);