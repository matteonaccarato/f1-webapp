<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("controllers/auth/auth.php");

[$login_allowed, $user] = check_cookie();
if ($login_allowed || check_user_auth($user)) {
    error("-1", "Already registered.", "\controllers\auth\\registration.php", "/f1-webapp/views/private/dashboard.php");
    exit;
}

/* -- ERROR | fields NOT set -- */
if (isset($_POST["firstname"]) && isset($_POST["lastname"]) && isset($_POST["email"]) && isset($_POST["pass"]) && isset($_POST["confirm"]) /* && isset($_POST["date_of_birth"])*/) {

    $first_name = $_POST["firstname"];
    $last_name = $_POST["lastname"];
    $email = $_POST["email"];
    $password = $_POST["pass"];
    $confirm = $_POST["confirm"];
    $date_of_birth = $_POST["date_of_birth"] ?? "";
    $newsletter = isset($_POST["newsletter"])? 1:0;

    $first_name = preg_replace('/\s+/', '', $first_name);
    $last_name = preg_replace('/\s+/', '', $last_name);
    $email = preg_replace('/\s+/', '', $email);
    $date_of_birth = preg_replace('/\s+/', ' ', $date_of_birth);
    $password = trim($password);
    $confirm = trim($confirm);

    /* -- ERROR | Empty input fields -- */
    if ($first_name == "" || $first_name == " "
        || $last_name == "" || $last_name == " "
        || $email == "" || $email == " "
        || $password == ""
        || $confirm == ""
        /*|| $date_of_birth == "" || $date_of_birth == " "*/) {
        error("-1", "Empty input fields.", "\controllers\auth\\registration.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }

    // REGEX EMAIL
    /* https://en.wikipedia.org/wiki/Email_address */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error("-1", "EMAIL pattern NOT valid.", "\controllers\auth\\registration.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }

    // REGEX DATE OF BIRTH dd/mm/yyyy
    if ($date_of_birth && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date_of_birth)) {
        error("-1", "Date of birth pattern NOT valid.", "\controllers\auth\\registration.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }

    /* -- ERROR | passwords (mis)matching -- */
    if ($password != $confirm) {
        error("-1", "Mismatched passwords.", "\controllers\auth\\registration.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }
    $hash_password = password_hash($password, PASSWORD_DEFAULT);
    

    /* CHECK INPUT LENGTHS */
    // $input_array = [$id, $first_name, $last_name, $email, $password, $role, $date_of_birth, $cookie_id, $img_url, $newsletter$];
    foreach ([null, $first_name, $last_name, $email, $hash_password, 0, $date_of_birth, null, null, $newsletter] as $index => $input) {
        if (USERS_MAX_LENGTHS[$index] >= 0 && $input && strlen($input) > USERS_MAX_LENGTHS[$index]) {
            $tmp = ucfirst(USERS_ARRAY[$index]);
            error("500", "$tmp is TOO long.", "\controllers\auth\\registration.php", "/f1-webapp/views/public/auth/registration.php");
            exit;
        }
    }

    /* DB */
    $conn = DB::connect("\controllers\auth\\registration.php", "/f1-webapp/views/public/auth/registration.php");

    DB::p_stmt_no_select($conn,
        "INSERT INTO Users VALUES (NULL, ?, ?, ?, ?, 0, ?, null, null, ?);",
        ["s", "s", "s", "s", "s", "i"],
        [$first_name, $last_name, $email, $hash_password, $date_of_birth, $newsletter],
        "\controllers\auth\\registration.php",
        "/f1-webapp/views/public/auth/registration.php");

    if (!$conn->close()) {
        error("500", "conn_close()", "\controllers\auth\\registration.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }


    $_SESSION["success"] = 1;
    $_SESSION["success_msg"] = "Registration completed successfully.";
    header("Location: /f1-webapp/views/public/auth/login.php");

} else {
    error("-1", "Input fields NOT provided.", "\controllers\auth\\registration.php", "/f1-webapp/views/public/auth/registration.php");
    exit;
}