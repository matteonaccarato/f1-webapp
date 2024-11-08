<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once ("utility/utility_func.php");
require_once("utility/error_handling.php");
require_once ("DB/DB.php");
require_once ("controllers/auth/auth.php");

const NEW_PASSWORD_LENGTH = 5;

[$login_allowed, $user] = check_cookie();
if (!$login_allowed && !check_user_auth($user)) {

    if (isset($_POST["email"])) {

        /* CLEANING INPUT */
        $email = preg_replace('/\s+/', '', $_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error("-1", "Email not an email", "controllers\auth\lost_password.php", "/f1-webapp/views/public/auth/lost_password.php");
            exit;
        }

        /* DB */
        $conn = DB::connect("controllers\auth\lost_password.php", "/f1-webapp/views/public/auth/lost_password.php");

        $user_db = DB::get_record_by_field($conn,
            "SELECT id, email FROM Users WHERE email = ?;",
            ['s'],
            [$email],
            "controllers\auth\lost_password.php",
            "/f1-webapp/views/public/auth/lost_password.php")[0];

        if ($user_db) {
            // EMAIL exists in DB
            // Generate new password and send it via mail
            try {
                $new_password = generate_random_string(NEW_PASSWORD_LENGTH);
                $hash_pwd = password_hash($new_password, PASSWORD_DEFAULT);

                DB::p_stmt_no_select($conn,
                    "UPDATE Users SET password = ? WHERE id = ?;",
                    ["s", "i"],
                    [$hash_pwd, $user_db["id"]],
                    "controllers\auth\lost_password.php",
                    "/f1-webapp/views/public/auth/lost_password.php");

                if (!$conn->close()) {
                    error("500", "conn_close()", "controllers\auth\lost_password.php", "/f1-webapp/views/public/auth/lost_password.php");
                    exit;
                }

                $body = "Your new credentials:<br>";
                $body .= "EMAIL: $email<br>";
                $body .= "New password: $new_password";
                send_mail([$user_db["email"]], "F1 INFO: here's your new password!", $body);

                $_SESSION["success"] = 1;
                $_SESSION["success_msg"] = "New password sent successfully!";
                header("Location: /f1-webapp/views/public/auth/login.php");;
                exit;
            } catch (Exception $e) {
                error("-1", "Exception: $e", "controllers\auth\lost_password.php", "/f1-webapp/views/public/auth/lost_password.php");
                exit;
            }

        } else {
            // EMAIL DOES NOT exists in DB
            error("-1", "Email DOES NOT exists.", "controllers\auth\lost_password.php", "/f1-webapp/views/public/auth/lost_password.php");
            exit;
        }

    } else {
        error("401", "Fields not provided.", "controllers\auth\lost_password.php", "/f1-webapp/views/public/auth/lost_password.php");
        exit;
    }
} else {
    header("Location: /f1-webapp/views/public/index.php");
    exit;
}