<?php

require_once "../../includes/cors.php";

require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| FETCH SPECIALIZATIONS
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    id,
    name,
    status,
    created_at

FROM specialization_masters

ORDER BY name ASC
";

$stmt = $conn->prepare($query);

$stmt->execute();

$specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Specializations fetched successfully", $specializations);