<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| GET SEARCH KEYWORD
|--------------------------------------------------------------------------
*/

$keyword = $_GET['keyword'] ?? '';

if(empty($keyword)) {

    error("Search keyword is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| SEARCH DOCTORS
|--------------------------------------------------------------------------
*/

$query = "
SELECT
    id,
    name,
    email,
    phone,
    profile_image,
    description,
    qualification,
    consulting_fee,
    availability_status
FROM doctors
WHERE
    status = 1
AND
(
    name LIKE ?
    OR qualification LIKE ?
    OR description LIKE ?
    OR availability_status LIKE ?
)
ORDER BY id DESC
";

$searchKeyword = "%" . $keyword . "%";

$stmt = $conn->prepare($query);

$stmt->execute([

    $searchKeyword,
    $searchKeyword,
    $searchKeyword,
    $searchKeyword
]);

$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

success("Doctors fetched successfully", $doctors);