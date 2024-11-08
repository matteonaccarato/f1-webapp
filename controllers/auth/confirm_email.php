<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("controllers/auth/auth.php");

[$login_allowed, $user] = check_cookie();
if (!$login_allowed && !check_user_auth($user)) {
    if (isset($_SESSION["tmp_firstname"]) && isset($_SESSION["tmp_lastname"]) && isset($_SESSION["tmp_email"]) && isset($_SESSION["tmp_pass"]) && isset($_SESSION["tmp_cpass"]) && isset($_SESSION["tmp_hpass"])) {

        $conn = DB::connect("\auth\confirm_email.php", "/f1-webapp/views/public/registration.php");

        $hash_password = $_SESSION["tmp_hpass"];

        unset($_SESSION["tmp_firstname"]);
        unset($_SESSION["tmp_lastname"]);
        unset($_SESSION["tmp_email"]);
        unset($_SESSION["tmp_pass"]);
        unset($_SESSION["tmp_cpass"]);
        unset($_SESSION["tmp_hpass"]);

        if (isset($_SESSION["tmp_date_of_birth"])) {
            $date_of_birth = $_SESSION["tmp_date_of_birth"];
            unset($_SESSION["tmp_date_of_birth"]);
        }

        if(isset($_SESSION["tmp_news"])){
            $newsletter = $_SESSION["tmp_news"];
            unset($_SESSION["tmp_news"]);
        }

        DB::p_stmt_no_select($conn,
            "INSERT INTO Users VALUES (NULL, ?, ?, ?, ?, 0, ?, null, null, ?);",
            ["s", "s", "s", "s", "s", "i"],
            [$first_name, $last_name, $email, $hash_password, $date_of_birth??null, $newsletter??null],
            "\auth\confirm_email.php",
            "/f1-webapp/views/public/registration.php");

        if (!$conn->close()) {
            error("500", "conn_close()", "\auth\confirm_email.php", "/f1-webapp/views/public/registration.php");
            exit;
        }

        $_SESSION["success"] = 1;
        $_SESSION["success_msg"] = "Registration completed successfully.";
        $_SESSION["confirm_email"] = true;
        header("Location: /f1-webapp/views/public/confirm.html");
    } else {
        session_destroy();
        error("-1", "Input fields NOT provided.", "\auth\confirm_email.php", "/f1-webapp/views/public/registration.php");
        exit;
    }
} else {
    header("Location: /f1-webapp/views/public/index.php");
    exit;
}