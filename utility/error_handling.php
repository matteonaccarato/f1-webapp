<?php
if (!defined("BASE_INCLUDE_PATH")) {
    // define("BASE_INCLUDE_PATH", "/chroot/home/S5330843/public_html/f1-webapp/"); // on SERVER
    define("BASE_INCLUDE_PATH", $_SERVER["DOCUMENT_ROOT"]); // on LOCAL
}
if(session_status() == PHP_SESSION_NONE) session_start();

$_DEFAULT_ERROR_REDIRECT = "/f1-webapp/views/public/index.php";

/**
 * Custom function to handle errors generated executing PHP scripts
 * @param string $e_code: error code
 * @param string $e_msg: error message
 * @param string $source: source file which has generated the error
 * @param string $redirect: filepath where to redirect
 * @param string $e_usr_msg: explicit error message (if needed)
 * @return void
 */
function error(string $e_code = "", string $e_msg = "", string $source = "", string $redirect = "", string $e_usr_msg = "") : void {
    global $_DEFAULT_ERROR_REDIRECT;

    $path = realpath(BASE_INCLUDE_PATH) . "\\$source";
    $e_json = "{\n" .
        "\t\"path\": \"$path\",\n" .
        "\t\"error_code\": \"$e_code\",\n " .
        "\t\"error_message\": \"$e_msg\"\n" .
        "},\n";
    $path = BASE_INCLUDE_PATH . "/errors.log";
    error_log($e_json, 3, $path);

    $_SESSION["err"] = 1;
    $_SESSION["err_msg"] = ($e_usr_msg != "")? $e_usr_msg:$e_msg;
    $redirect = ($redirect != "")? $redirect : $_DEFAULT_ERROR_REDIRECT;
    header("Location: $redirect");
    exit;
}