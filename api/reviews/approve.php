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

$review_id = $_POST['review_id'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($review_id)) {

    error("Review ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK REVIEW EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id, is_approved
FROM reviews
WHERE id = ?
AND status = 1
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$review_id]);

$review = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$review) {

    error("Review not found");
    exit;
}

/*
|--------------------------------------------------------------------------
| ALREADY APPROVED
|--------------------------------------------------------------------------
*/

if($review['is_approved'] == 1) {

    error("Review already approved");
    exit;
}

/*
|--------------------------------------------------------------------------
| APPROVE REVIEW
|--------------------------------------------------------------------------
*/

$approveQuery = "
UPDATE reviews
SET
    is_approved = 1,
    updated_at = NOW()
WHERE id = ?
";

$approveStmt = $conn->prepare($approveQuery);

$isApproved = $approveStmt->execute([

    $review_id
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isApproved) {

    success("Review approved successfully");

} else {

    error("Review approval failed");
}