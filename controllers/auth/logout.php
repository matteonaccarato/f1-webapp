<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once("DB/DB.php");
require_once("controllers/auth/auth.php");

if (check_user_auth()) {

    /* DELETE COOKIE */
    if (isset($_COOKIE["my_f1_cookie_id"]) && isset($_COOKIE["my_f1_cookie_value"]) &&
        $_COOKIE["my_f1_cookie_id"] && $_COOKIE["my_f1_cookie_value"]) {
        $conn = DB::connect("\controllers\auth\logout.php", "/f1-webapp/views/private/dashboard.php");
        DB::p_stmt_no_select($conn,
            "DELETE FROM Cookies WHERE id = ?;",
            ["s"],
            [$_COOKIE["my_f1_cookie_id"]],
            "\logout.php",
            "/f1-webapp/views/public/auth/login.php");

        if (!$conn->close()) {
            error("500", "conn_close: $conn->error", "\logout.php", "/f1-webapp/views/public/auth/login.php");
            exit;
        }

        $cookie_id = null;
        $cookie_exp_date = time() - 3600;
        setcookie("my_f1_cookie_id", $cookie_id, $cookie_exp_date);
    }

    session_destroy();

    $_SESSION["success"] = 1;
    $_SESSION["success_msg"] = "Successfully logged out.";

    header("Location: /f1-webapp/views/public/auth/login.php");

} else {
    $_SESSION['redirection'] = "/f1-webapp/controllers/auth/logout.php";
    error("401", "not_authorized", "\logout.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}