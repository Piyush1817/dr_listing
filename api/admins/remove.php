<?php

require_once "../../includes/cors.php";
require_once "../../includes/admin_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET ADMIN ID
|--------------------------------------------------------------------------
*/

$id = $_POST['id'] ?? '';

$current_admin_id = $_SESSION['admin_id'];

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if(empty($id)) {

    error("Admin ID is required");
}

/*
|--------------------------------------------------------------------------
| PREVENT SELF DELETE
|--------------------------------------------------------------------------
*/

if($id == $current_admin_id) {

    error("You cannot remove yourself");
}

/*
|--------------------------------------------------------------------------
| CHECK ADMIN EXISTS
|--------------------------------------------------------------------------
*/

$checkQuery = "
SELECT id
FROM admins
WHERE id = ?
LIMIT 1
";

$checkStmt = $conn->prepare($checkQuery);

$checkStmt->execute([$id]);

$admin = $checkStmt->fetch(PDO::FETCH_ASSOC);

if(!$admin) {

    error("Admin not found");
}

/*
|--------------------------------------------------------------------------
| DELETE ADMIN
|--------------------------------------------------------------------------
*/

$deleteQuery = "
DELETE FROM admins
WHERE id = ?
";

$deleteStmt = $conn->prepare($deleteQuery);

$isDeleted = $deleteStmt->execute([$id]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isDeleted) {

    success("Admin removed successfully");

} else {

    error("Admin remove failed");
}