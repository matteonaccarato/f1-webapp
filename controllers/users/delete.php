<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("DB/DB.php");
require_once("utility/utility_func.php");
require_once ("utility/aws.php");

const SOURCE = "\controllers\users\delete.php";
const REDIRECT = "/f1-webapp/views/private/users/all.php";

[$login_allowed, $user] = check_cookie();
if (!check_admin_auth($user)) {
    $_SESSION['redirection'] = "/f1-webapp/users/delete.php?id={${${$_GET['id']??''}}}";
    error("401", "not_authorized", SOURCE, "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
set_session($user);

if (!isset($_GET["id"]) || !$_GET["id"]) {
    error("500", "id_not_given", SOURCE, REDIRECT, "ID NOT given.");
    exit;
}

unset($_SESSION["dont_show_in_nav"]);

if($_SESSION["id"] == $_GET["id"]) {
    error("500", "You cannot delete the account you are using.", SOURCE, REDIRECT);
    exit;
}

/* Retrieve role and image url */
$conn = DB::connect(SOURCE, REDIRECT);
$row = DB::get_record_by_field($conn,
    "SELECT role, img_url FROM Users WHERE id = ?;",
    ["i"],
    [intval($_GET["id"])],
    SOURCE,
    REDIRECT)[0];
if (!$conn->close()) {
    error("-1", "conn->close", SOURCE, REDIRECT);
    exit;
}
$role = $row["role"];
$image = ($row["img_url"] != "")? $row["img_url"]:-1;
if ($row["role"] == 1) {
    error("-1", "You cannot delete an Administrator.", SOURCE, REDIRECT);
    exit;
}
if (preg_match("#^http://f1-saw.s3.eu-central-1.amazonaws.com/*#", $row["img_url"])) {
    aws_delete_img($row["img_url"]);
}

/* DELETE User */
$conn = DB::connect(SOURCE, REDIRECT);
DB::p_stmt_no_select($conn,
    "DELETE FROM Users WHERE id = ?;",
    ["i"],
    [$_GET["id"]],
    SOURCE,
    REDIRECT);

if (!$conn->close()) {
    error("500", "conn_close()", SOURCE, REDIRECT);
    exit;
}

$_SESSION["success"] = 1;
$_SESSION["success_msg"] = "Account deleted.";
header("location:  " . REDIRECT);