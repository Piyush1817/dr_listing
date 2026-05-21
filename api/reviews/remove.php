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
SELECT id
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
| REMOVE REVIEW
|--------------------------------------------------------------------------
*/

$removeQuery = "
UPDATE reviews
SET
    status = 0,
    updated_at = NOW()
WHERE id = ?
";

$removeStmt = $conn->prepare($removeQuery);

$isRemoved = $removeStmt->execute([

    $review_id
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isRemoved) {

    success("Review removed successfully");

} else {

    error("Review removal failed");
}