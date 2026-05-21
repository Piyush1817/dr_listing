<?php

require_once "../../includes/cors.php";
require_once "../../includes/doctor_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

/*
|--------------------------------------------------------------------------
| CHECK IMAGE
|--------------------------------------------------------------------------
*/

if(!isset($_FILES['profile_image'])) {

    error("Profile image is required");
    exit;
}

/*
|--------------------------------------------------------------------------
| GET FILE
|--------------------------------------------------------------------------
*/

$file = $_FILES['profile_image'];

$fileName = $file['name'];

$tmpName = $file['tmp_name'];

$fileSize = $file['size'];

$error = $file['error'];

/*
|--------------------------------------------------------------------------
| VALIDATE ERROR
|--------------------------------------------------------------------------
*/

if($error != 0) {

    error("Image upload failed");
    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDATE EXTENSION
|--------------------------------------------------------------------------
*/

$allowedExtensions = [

    "jpg",
    "jpeg",
    "png",
    "webp"
];

$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if(!in_array($fileExtension, $allowedExtensions)) {

    error("Invalid image format");
    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDATE FILE SIZE
|--------------------------------------------------------------------------
*/

$maxSize = 2 * 1024 * 1024;

if($fileSize > $maxSize) {

    error("Image size should be less than 2MB");
    exit;
}

/*
|--------------------------------------------------------------------------
| GENERATE UNIQUE FILE NAME
|--------------------------------------------------------------------------
*/

$newFileName = time() . "_" . uniqid() . "." . $fileExtension;

/*
|--------------------------------------------------------------------------
| UPLOAD PATH
|--------------------------------------------------------------------------
*/

$uploadPath = "../../uploads/doctors/" . $newFileName;

/*
|--------------------------------------------------------------------------
| MOVE FILE
|--------------------------------------------------------------------------
*/

if(!move_uploaded_file($tmpName, $uploadPath)) {

    error("Failed to upload image");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE DOCTOR PROFILE IMAGE
|--------------------------------------------------------------------------
*/

$doctorId = $_SESSION['doctor_id'];

$updateQuery = "
UPDATE doctors
SET
    profile_image = ?,
    updated_at = NOW()
WHERE id = ?
";

$updateStmt = $conn->prepare($updateQuery);

$isUpdated = $updateStmt->execute([

    $newFileName,
    $doctorId
]);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if($isUpdated) {

    success("Profile image uploaded successfully", [

        "image" => $newFileName
    ]);

} else {

    error("Database update failed");
}