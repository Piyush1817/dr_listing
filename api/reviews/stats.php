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
| FETCH REVIEW STATS
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    ROUND(AVG(rating), 1) AS average_rating,
    COUNT(id) AS total_reviews

FROM reviews

WHERE
    doctor_id = ?
AND
    is_approved = 1
AND
    status = 1
";

$stmt = $conn->prepare($query);

$stmt->execute([$doctor_id]);

$stats = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| HANDLE EMPTY REVIEWS
|--------------------------------------------------------------------------
*/

if(!$stats['average_rating']) {

    $stats['average_rating'] = 0;
}

if(!$stats['total_reviews']) {

    $stats['total_reviews'] = 0;
}

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Review stats fetched successfully", $stats);