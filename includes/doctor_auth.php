<?php

session_start();

/*
|--------------------------------------------------------------------------
| CHECK DOCTOR LOGIN
|--------------------------------------------------------------------------
*/

if(!isset($_SESSION['doctor_id'])) {

    echo json_encode([

        "status" => false,
        "message" => "Unauthorized access",
        "data" => []

    ]);

    exit;
}