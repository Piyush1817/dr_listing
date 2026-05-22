<?php

require_once "../../includes/cors.php";
require_once "../../includes/admin_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET ADMINS
|--------------------------------------------------------------------------
*/

$query = "
SELECT
    id,
    name,
    email,
    created_at
FROM admins
ORDER BY id DESC
";

$stmt = $conn->prepare($query);

$stmt->execute();

$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Admins fetched successfully", $admins);