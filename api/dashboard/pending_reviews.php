<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| FETCH PENDING REVIEWS
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    reviews.id,
    reviews.patient_name,
    reviews.patient_email,
    reviews.rating,
    reviews.review_title,
    reviews.review_text,
    reviews.created_at,

    doctors.name AS doctor_name

FROM reviews

LEFT JOIN doctors
ON doctors.id = reviews.doctor_id

WHERE
    reviews.is_approved = 0
AND
    reviews.status = 1

ORDER BY reviews.created_at DESC
";

$stmt = $conn->prepare($query);

$stmt->execute();

$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Pending reviews fetched successfully", $reviews);