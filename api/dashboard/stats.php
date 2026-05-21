<?php

require_once "../../includes/cors.php";
require_once "../../includes/admin_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| TOTAL DOCTORS
|--------------------------------------------------------------------------
*/

$totalDoctorsQuery = "
SELECT COUNT(id) AS total_doctors
FROM doctors
WHERE status = 1
";

$totalDoctorsStmt = $conn->prepare($totalDoctorsQuery);

$totalDoctorsStmt->execute();

$totalDoctors = $totalDoctorsStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| PENDING DOCTORS
|--------------------------------------------------------------------------
*/

$pendingDoctorsQuery = "
SELECT COUNT(id) AS pending_doctors
FROM doctors
WHERE status = 0
";

$pendingDoctorsStmt = $conn->prepare($pendingDoctorsQuery);

$pendingDoctorsStmt->execute();

$pendingDoctors = $pendingDoctorsStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| TOTAL HOSPITALS
|--------------------------------------------------------------------------
*/

$totalHospitalsQuery = "
SELECT COUNT(id) AS total_hospitals
FROM hospitals
WHERE status = 1
";

$totalHospitalsStmt = $conn->prepare($totalHospitalsQuery);

$totalHospitalsStmt->execute();

$totalHospitals = $totalHospitalsStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| TOTAL REVIEWS
|--------------------------------------------------------------------------
*/

$totalReviewsQuery = "
SELECT COUNT(id) AS total_reviews
FROM reviews
WHERE status = 1
";

$totalReviewsStmt = $conn->prepare($totalReviewsQuery);

$totalReviewsStmt->execute();

$totalReviews = $totalReviewsStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| PENDING REVIEWS
|--------------------------------------------------------------------------
*/

$pendingReviewsQuery = "
SELECT COUNT(id) AS pending_reviews
FROM reviews
WHERE is_approved = 0
AND status = 1
";

$pendingReviewsStmt = $conn->prepare($pendingReviewsQuery);

$pendingReviewsStmt->execute();

$pendingReviews = $pendingReviewsStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| FINAL RESPONSE
|--------------------------------------------------------------------------
*/

$data = [

    "total_doctors" => (int)$totalDoctors['total_doctors'],

    "pending_doctors" => (int)$pendingDoctors['pending_doctors'],

    "total_hospitals" => (int)$totalHospitals['total_hospitals'],

    "total_reviews" => (int)$totalReviews['total_reviews'],

    "pending_reviews" => (int)$pendingReviews['pending_reviews']
];

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Dashboard stats fetched successfully", $data);