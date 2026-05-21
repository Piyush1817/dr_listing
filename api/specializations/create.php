<?php

require_once "../../includes/cors.php";
require_once "../../includes/doctor_auth.php";
require_once "../../includes/response.php";
require_once "../../config/db.php";

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*
|--------------------------------------------------------------------------
| GET INPUT
|--------------------------------------------------------------------------
*/

$doctor_id = $_POST['doctor_id'] ?? '';
$specialization_id = $_POST['specialization_id'] ?? '';

if (empty($doctor_id) || empty($specialization_id)) {
    error("Doctor ID and Specialization ID are required");
    exit;
}

try {

    /*
    |--------------------------------------------------------------------------
    | CHECK DOCTOR
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT id
        FROM doctors
        WHERE id = ? AND status = 1
        LIMIT 1
    ");
    $stmt->execute([$doctor_id]);

    if (!$stmt->fetch()) {
        error("Doctor not found");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK SPECIALIZATION
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT id
        FROM specialization_masters
        WHERE id = ? AND status = 1
        LIMIT 1
    ");
    $stmt->execute([$specialization_id]);

    if (!$stmt->fetch()) {
        error("Specialization not found");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK EXISTING MAPPING
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT id, status
        FROM doctor_specializations
        WHERE doctor_id = ? AND specialization_id = ?
        LIMIT 1
    ");
    $stmt->execute([$doctor_id, $specialization_id]);

    $mapping = $stmt->fetch(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | ALREADY ACTIVE
    |--------------------------------------------------------------------------
    */

    if ($mapping && $mapping['status'] == 1) {
        error("Specialization already assigned");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | REACTIVATE
    |--------------------------------------------------------------------------
    */

    if ($mapping && $mapping['status'] == 0) {

        $stmt = $conn->prepare("
            UPDATE doctor_specializations
            SET status = 1, updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([$mapping['id']]);

        success("Specialization restored successfully");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | INSERT NEW
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        INSERT INTO doctor_specializations
        (doctor_id, specialization_id, status, created_at)
        VALUES (?, ?, 1, NOW())
    ");

    $stmt->execute([$doctor_id, $specialization_id]);

    success("Specialization assigned successfully");

} catch (PDOException $e) {
    error("DB Error: " . $e->getMessage());
}