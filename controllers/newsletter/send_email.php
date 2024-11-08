<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("utility/utility_func.php");

[$login_allowed, $user] = check_cookie();
if (check_admin_auth($user)) {
    set_session($user);

    if (isset($_POST["subject"]) && $_POST["subject"] && isset($_POST["text"]) && $_POST["text"]) {

        $subject = preg_replace('/\s+/', '', $_POST["subject"]);
        $body = preg_replace('/\s+/', '', $_POST["text"]);

        if (!$subject  || !$body) {
            error("500", "Fields NOT provided", "send_email.php", "/f1-webapp/views/private/newsletter/newsletter.php");
            exit;
        }

        $subject = $_POST["subject"];
        $body = $_POST["text"];

        $conn = DB::connect("send_email.php", "/f1-webapp/views/private/newsletter/send_email.php");
        [$num_users, $recipients] = DB::stmt_get_record_by_field($conn,
            "SELECT email FROM users WHERE newsletter = 1;",
        "send_email.php",
        "/f1-webapp/views/private/newsletter/send_email.php");
        if (!$conn->close()) {
            error("500", "conn_close()", "send_email.php", "/f1-webapp/views/private/newsletter/send_email.php");
            exit;
        }

        try {
            $ini = parse_ini_file("config/keys.ini");
            send_mail([$ini["g_email"]], $subject, $body, $recipients);

            $_SESSION["success"] = 1;
            $_SESSION["success_msg"] = "Email SENT successfully :)";
            header("Location: /f1-webapp/views/private/newsletter/newsletter.php");
            exit;

        } catch (Exception $e) {
            error("500", "PHPMailer exception: $e", "send_email.php", "/f1-webapp/views/private/newsletter/newsletter.php");
            exit;
        }
    } else {
        error("500", "Fields NOT provided", "send_email.php", "/f1-webapp/views/private/newsletter/newsletter.php");
        exit;
    }
} else {
    $_SESSION['redirection'] = "/f1-webapp/controllers/auth/newsletter/send_email.php";
    error("401", "Unauthorized access", "send_email.php", "/f1-webapp/views/public/auth/login.php");
    exit;
}