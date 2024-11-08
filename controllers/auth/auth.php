<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once "utility/error_handling.php";
require_once "DB/DB.php";

function set_session($user): void {
    if ($user && $user != []) {
        $_SESSION["s_id"] = session_id();
        $_SESSION["id"] = $user["Users.id"];
        $_SESSION["email"] = $user["Users.email"];
        $_SESSION["first_name"] = $user["Users.first_name"];
        $_SESSION["last_name"] = $user["Users.last_name"];
        $_SESSION["date_of_birth"] = $user["Users.date_of_birth"];
        $_SESSION["img_url"] = $user["Users.img_url"];
        $_SESSION["newsletter"] = $user["Users.newsletter"];
        $_SESSION["role"] = $user["Users.role"];
        $_SESSION["cookie_id"] = $user["Cookies.id"];
    }
}

function check_cookie(): array {
    $user = [];
    $login_allowed = 0;

    if (isset($_COOKIE["my_f1_cookie_id"]) && isset($_COOKIE["my_f1_cookie_value"])) {
        $conn = DB::connect("\controllers\auth\auth.php", "/f1-webapp/views/public/index.php");
        $user = DB::get_record_by_field($conn,
            "SELECT Users.id AS 'Users.id', Users.first_name AS 'Users.first_name', Users.last_name AS 'Users.last_name', Users.email AS 'Users.email', Users.password AS 'Users.password', Users.role AS 'Users.role', Users.date_of_birth AS 'Users.date_of_birth', Users.img_url AS 'Users.img_url', Users.newsletter AS 'Users.newsletter', Cookies.id AS 'Cookies.id', Cookies.value AS 'Cookies.value', Cookies.expiration_date AS 'Cookies.expiration_date' FROM Users JOIN Cookies ON Users.cookie_id = Cookies.id WHERE Cookies.id = ? AND Cookies.expiration_date > ?;",
            ["s", "i"],
            [$_COOKIE["my_f1_cookie_id"], time()])[0];
        if (!password_verify($_COOKIE["my_f1_cookie_value"], $user["Cookies.value"]))
            $user = null;
        if (!$conn->close()) {
            error("500", "conn close error: $conn->error", "\controllers\auth\auth.php", "/f1-webapp/views/public/index.php");
            exit;
        }
        if ($user !== null)
            $login_allowed = 1;
    }
    return [$login_allowed, $user];
}
function check_admin_auth($user = null): bool {
    return check_auth(1, $user);
}

function check_user_auth($user = null): bool {
    return check_auth(0, $user);
}

function check_auth($role, $user = null): bool {
    if ($user) {
        return $user["Users.role"] >= $role;
    }

    [$login_allowed, $user] = check_cookie();
    return (($login_allowed && $user["Users.role"] >= $role)
        || isset($_SESSION["role"]) && $_SESSION["role"] >= $role);
}