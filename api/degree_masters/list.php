<?php

require_once "../../includes/cors.php";

require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| FETCH DEGREES
|--------------------------------------------------------------------------
*/

$query = "

SELECT

    id,
    name,
    status,
    created_at

FROM degree_masters

ORDER BY name ASC
";

$stmt = $conn->prepare($query);

$stmt->execute();

$degrees = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Degrees fetched successfully", $degrees);