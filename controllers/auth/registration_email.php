<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("controllers/auth/auth.php");
require_once("utility/utility_func.php");

[$login_allowed, $user] = check_cookie();
if ($login_allowed || check_user_auth($user)) {
    error("-1", "Already registered.", "\auth\\registration_email.php", "/f1-webapp/views/private/dashboard.php");
    exit;
}

/* -- ERROR | fields NOT set -- */
if (isset($_POST["firstname"]) && isset($_POST["lastname"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])) {

    $first_name = $_POST["firstname"];
    $last_name = $_POST["lastname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];
    $date_of_birth = $_POST["date_of_birth"] ?? "";
    $newsletter = isset($_POST["newsletter"]) ? 1 : 0;

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
        error("-1", "Empty input fields.", "\auth\\registration_email.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }


    // REGEX EMAIL
    /* https://en.wikipedia.org/wiki/Email_address */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error("-1", "EMAIL pattern NOT valid.", "\auth\\registration_email.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }


    // REGEX DATE OF BIRTH dd/mm/yyyy
    if ($date_of_birth && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date_of_birth)) {
        error("-1", "Date of birth pattern NOT valid.", "\auth\\registration_email.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }

    /* -- ERROR | passwords (mis)matching -- */
    if ($password != $confirm) {
        error("-1", "Mismatched passwords.", "\auth\\registration_email.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }
    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $subject = "Confirm email";
        $body = "
                    <!DOCTYPE html>
                    <html lang='en'>
                    
                    <head>
                        <title>Confirm email</title>
                        <meta charset='UTF-8'>
                    </head>
                    <body>
                    <p>Hi $first_name! We have just received your request to register on our site. Please confirm your email to complete registration: <a href='http://localhost:63342/f1-webapp/controllers/auth/confirm_email.php'>confirm email</p>
                    </body>
                    ";
        send_mail([$email], $subject, $body);
        $_SESSION["confirm_email"] = false;

        // I need to temporarily keep the recording values in memory
        $_SESSION["tmp_firstname"] = $first_name;
        $_SESSION["tmp_lastname"] = $last_name;
        $_SESSION["tmp_email"] = $email;
        $_SESSION["tmp_date_of_birth"] = $date_of_birth;
        $_SESSION["tmp_pass"] = $password;
        $_SESSION["tmp_cpass"] = $confirm;
        $_SESSION["tmp_hpass"] = $hash_password;
        $_SESSION["tmp_news"] = $newsletter;

        header("location: /f1-webapp/views/public/auth/registration.php");

    } catch (Exception $e) {
        error("500", "PHPMailer exception: $e", "\auth\\registration_email.php", "/f1-webapp/views/public/auth/registration.php");
        exit;
    }

} else {
    error("-1", "Input fields NOT provided.", "\auth\\registration_email.php", "/f1-webapp/views/public/auth/registration.php");
    exit;
}