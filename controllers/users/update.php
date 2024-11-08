<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("utility/utility_func.php");
require_once("utility/aws.php");

const SOURCE = "\controllers\users\update.php";
define("REDIRECT", "/f1-webapp/views/private/users/show_profile.php" . (($_POST["id_to_update"] != null) ? ("?id=" . $_POST["id_to_update"]) : ""));

const MAX_NUMBER_FILES = 1;


[$login_allowed, $user] = check_cookie();
if (!check_user_auth($user)) {
    $_SESSION['redirection'] = "/f1-webapp/controllers/users/update.php?id={${${$_POST['id_to_update']??''}}}";
    error("401", "not_authorized", "\controllers\users\update.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
set_session($user);


/* ERROR | Fields NOT set */
if (!isset($_POST["firstname"]) || !isset($_POST["lastname"]) || !isset($_POST["email"])) {
    error("-1", "Input fields NOT provided", SOURCE, REDIRECT);
    exit;
}

/* ERROR | Empty input fields */
if (preg_match("/\s+/", $_POST["firstname"])
    || preg_match("/\s+/", $_POST["lastname"])
    || preg_match("/\s+/", $_POST["email"])) {
    error("-1", "Input fields are EMPTY", SOURCE, REDIRECT);
    exit;
}

/* Cleaning input */
$id_to_update = (isset($_POST["id_to_update"]) && !preg_match("/\s+/", $_POST["id_to_update"]))? $_POST["id_to_update"]:$_SESSION["id"];
$firstname = preg_replace("/\s+/", "", $_POST["firstname"]);
$lastname = preg_replace("/\s+/", "", $_POST["lastname"]);
$email = preg_replace("/\s+/", "", $_POST["email"]);
$date_of_birth = preg_replace("/\s+/", "", $_POST["edit_date_of_birth"]??"");
$password = trim(preg_replace("/\s+/", "", $_POST["pass"]??""));
$password_confirm = trim(preg_replace("/\s+/", "", $_POST["pass_confirm"]??""));
$img_url = preg_replace("/\s+/", "", $_POST["edit_img"]??"");
$newsletter = $_POST["edit_news"]??0;
$role = $_POST["edit_role"]??-1; // -1 means that role input has not been updated

/* Regex Email */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error("-1", "EMAIL pattern NOT valid.", SOURCE, REDIRECT);
    exit;
}

// REGEX DATE OF BIRTH dd/mm/yyyy
if ($date_of_birth != "" &&!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date_of_birth)) {
    error("-1", "Date of birth pattern NOT valid.", SOURCE, REDIRECT);
    exit;
}

// Role
if ($role != -1 && $role != 0 && $role != 1) {
    error("-1", "Role pattern NOT valid. Please provide a number between 0 and 1.", SOURCE, REDIRECT);
    exit;
}

// Newsletter
if ($newsletter != 0 && $newsletter != 1) {
    error("-1", "Newsletter pattern NOT valid. Please provide a number between 0 and 1.", SOURCE, REDIRECT);
    exit;
}

/* ERROR | passwords (mis)matching */
if ($password != $password_confirm) {
    error("-1", "Mismatched passwords.", SOURCE, REDIRECT);
    exit;
}

/* Password handling */
$password_change = 0;
$hash_password = "";
if ($password != "") {
    $hash_password = password_hash($password, PASSWORD_DEFAULT);
    $password_change = 1;
}

/* Role constraint */
$previous_image = "";
if ($role > -1) {
    // Role field has been updated, so I need to check if role constraint failed
    $conn = DB::connect(SOURCE, REDIRECT);
    $row = DB::get_record_by_field($conn,
        "SELECT role, img_url FROM Users WHERE id = ?;",
        ["i"],
        [$id_to_update],
        SOURCE,
        REDIRECT)[0];
    if (!$conn->close()) {
        error("-1", "conn->close", SOURCE, REDIRECT);
        exit;
    }
    $previous_role = $row["role"];
    $previous_image = ($row["img_url"] != "")? $row["img_url"]:-1;

    if ($previous_role == 1 && $role != 1) {
        error("-1", "You cannot modify the role of an Administrator.", SOURCE, REDIRECT);
        exit;
    }
}


/* IMAGE HANDLING */
// No image urls provided, so "images-local" should be taken into account
$s3_upload = 0;
if ($img_url == "" && isset($_FILES["image-local"]) && $_FILES["image-local"]["name"]) {

    // Make sure to look for empty file names and paths, the array might contain empty strings. Use array_filter() before count.
    // $files = array_filter($_FILES['image-local']['name']);
    // var_dump($_FILES["image-local"]);
    if (gettype($_FILES["image-local"]["name"]) !== "string") {
        // It is an array, so multiple files have been uploaded.
        // This is NOT allowed
        error("-1", "Max number of files exceeded.", SOURCE, REDIRECT);
        exit;
    }

    $s3_file_link = "";

    $file_temp_src = $_FILES["image-local"]["tmp_name"];
    $filename = $_FILES["image-local"]["name"];

    [$status, $statusMsg, $s3_file_link] = aws_s3_upload($filename, $file_temp_src);
    $s3_upload = 1;
    if ($status == "danger") {
        error("-1", "AWS S3: $statusMsg.", SOURCE, REDIRECT);
        exit;
    }
    $img_url = $s3_file_link;
}


// Look for the previous url image, if not done yet
if ($previous_image == "") {
    // Role field has been updated, so I need to check if role constraint failed
    $conn = DB::connect(SOURCE, REDIRECT);
    $row = DB::get_record_by_field($conn,
        "SELECT img_url FROM Users WHERE id = ?;",
        ["i"],
        [$id_to_update],
        SOURCE,
        REDIRECT)[0];
    if (!$conn->close()) {
        error("-1", "conn->close", SOURCE, REDIRECT);
        exit;
    }
    $previous_image = ($row["img_url"] != "")? $row["img_url"]:-1;
}

if ($previous_image != $img_url) {
    if (preg_match("#^http://f1-saw.s3.eu-central-1.amazonaws.com/*#", $previous_image)) {
        aws_delete_img($previous_image);
    }
}


/* CHECK INPUT LENGTHS */
// $input_array = [$id, $first_name, $last_name, $email, $password, $role, $date_of_birth, $cookie_id, $img_url, $newsletter$];
foreach ([null, $firstname, $lastname, $email, $hash_password, $role, $date_of_birth, null, $img_url, $newsletter] as $index => $input) {
    if (USERS_MAX_LENGTHS[$index] >= 0 && $input && strlen($input) > USERS_MAX_LENGTHS[$index]) {
        $tmp = ucfirst(USERS_ARRAY[$index]);
        if ($s3_upload) {
            aws_delete_img($img_url);
        }
        error("500", "$tmp is TOO long.", SOURCE, REDIRECT);
        exit;
    }
}


$password_query = $password_change?"password = ?, ":"";
$role_query = ($role > -1)?"role = ?, ":"";
$query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, $password_query date_of_birth = ?, $role_query img_url = ?, newsletter = ?
         WHERE id = ?;";
$type_params = ["s", "s", "s"];
$params = [$firstname, $lastname, $email];
if ($password_change) {
    $type_params[] = "s";
    $params[] = $hash_password;
}
$type_params[] = "s";
$params[] = $date_of_birth;
if ($role > -1) {
    $type_params[] = "i";
    $params[] = $role;
}
$type_params[] = "s";
$params[] = $img_url;
$type_params[] = "i";
$params[] = $newsletter;
$type_params[] = "i";
$params[] = $id_to_update;

/* DB */
$conn = DB::connect(SOURCE, REDIRECT);
DB::p_stmt_no_select(
    $conn,
    $query,
    $type_params,
    $params,
    SOURCE, REDIRECT
);
if (!$conn->close()) {
    error("500", "conn_close()", SOURCE, REDIRECT);
    exit;
}

if (!isset($_SESSION["err"]) || $_SESSION["err"] === 0) {
    $_SESSION["success"] = 1;
    $_SESSION["success_msg"] = "Update completed successfully.";
}

if(isset($_SESSION['redirection'])){
    header("Location: {$_SESSION['redirection']}");
    unset($_SESSION['redirection']);
    exit;
}

header("location: /f1-webapp/views/private/users/all.php");
exit;