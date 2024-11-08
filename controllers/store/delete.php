<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("DB/DB.php");
require_once("utility/utility_func.php");
require_once ("utility/aws.php");

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

[$login_allowed, $user] = check_cookie();
if (check_admin_auth($user)) {
    set_session($user);

    $conn = DB::connect("\controllers\store\delete.php", "/f1-webapp/views/private/store/all.php");

    // Get Image URLs to eventually delete (if they are stored on AWS-S3 and the delete statement does not fail
    $imgs_str = DB::get_record_by_field($conn,
        "SELECT img_url FROM Products WHERE id = ?;",
        ["i"],
        [$_GET["id"]],
        "\controllers\store\delete.php",
        "/f1-webapp/views/private/store/all.php")[0]["img_url"];
    $imgs = explode("\t", $imgs_str);

    /* Delete Products from DB */
    DB::p_stmt_no_select($conn,
        "DELETE FROM Products WHERE id = ?;",
        ["i"],
        [$_GET["id"]],
        "\controllers\store\delete.php",
        "/f1-webapp/views/private/store/all.php");

    // Delete AWS S3 images (if needed) when the deletion statement went right
    // Analyze each image
    foreach ($imgs as $img) {
        // If it matches, it means that the image is stored on AWS S3
        // I Have to delete it from the bucket
        if (preg_match("#^http://f1-saw.s3.eu-central-1.amazonaws.com/*#", $img)) {
            aws_delete_img($img);
        }
    }

    if (!$conn->close()) {
        error("500", "conn_close()", "\controllers\store\delete.php", "/f1-webapp/views/private/store/all.php");
        exit;
    }
    $_SESSION["success"] = 1;
    $_SESSION["success_msg"] = "Product deleted.";
    header("location:  /f1-webapp/views/private/store/all.php");
}
else {
    $_SESSION['redirection'] = "/f1-webapp/controllers/store/delete.php?id={${${$_GET['id']??''}}}";
    error("401", "not_authorized", "\controllers\store\delete.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
}
exit;