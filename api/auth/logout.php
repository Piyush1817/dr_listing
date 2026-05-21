<?php

require_once "../../includes/cors.php";
require_once "../../includes/response.php";

/*
|--------------------------------------------------------------------------
| START SESSION
|--------------------------------------------------------------------------
*/

session_start();

/*
|--------------------------------------------------------------------------
| GET ROLE
|--------------------------------------------------------------------------
*/

$role = $_POST['role'] ?? '';

/*
|--------------------------------------------------------------------------
| ADMIN LOGOUT
|--------------------------------------------------------------------------
*/

if($role == "admin") {

    if(!isset($_SESSION['admin_id'])) {

        error("Admin not logged in");
        exit;
    }

    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);

    success("Admin logout successful");
    exit;
}

/*
|--------------------------------------------------------------------------
| DOCTOR LOGOUT
|--------------------------------------------------------------------------
*/

if($role == "doctor") {

    if(!isset($_SESSION['doctor_id'])) {

        error("Doctor not logged in");
        exit;
    }

    unset($_SESSION['doctor_id']);
    unset($_SESSION['doctor_name']);
    unset($_SESSION['doctor_email']);

    success("Doctor logout successful");
    exit;
}

/*
|--------------------------------------------------------------------------
| INVALID ROLE
|--------------------------------------------------------------------------
*/

error("Invalid role");