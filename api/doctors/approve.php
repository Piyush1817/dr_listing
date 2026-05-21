<?php

require_once "../../includes/cors.php";
require_once "../../includes/admin_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET DOCTOR ID
|--------------------------------------------------------------------------
*/

$id = $_POST['id'] ?? '';

if(empty($id)) {

    error("Doctor ID is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| APPROVE DOCTOR
|--------------------------------------------------------------------------
*/

$query = "
UPDATE doctors
SET status = 1
WHERE id = ?
";

$stmt = $conn->prepare($query);

$isUpdated = $stmt->execute([$id]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isUpdated) {

    success("Doctor approved successfully");

} else {

    error("Approval failed");
}