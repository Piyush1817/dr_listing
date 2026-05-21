<?php

session_start();

/*
|--------------------------------------------------------------------------
| CHECK ADMIN LOGIN
|--------------------------------------------------------------------------
*/

if(!isset($_SESSION['admin_id'])) {

    echo json_encode([

        "status" => false,
        "message" => "Unauthorized access",
        "data" => []

    ]);

    exit;
}