<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("controllers/auth/auth.php");
require_once ("utility/aws.php");

const MAX_NUMBER_FILES = 2;

[$login_allowed, $user] = check_cookie();
if (check_admin_auth($user)) {

    if (isset($_POST["id"]) && isset($_POST["title"]) && isset($_POST["price"]) && isset($_POST["team_id"])) {

        /* CLEANING INPUT */
        $id = preg_replace('/\s+/', '', $_POST["id"]);
        $title = preg_replace('/\s+/', ' ', $_POST["title"]);
        $desc = preg_replace('/\s+/', ' ', $_POST["desc"]??"");
        $price = preg_replace('/\s+/', '', $_POST["price"]);
        $team_id = preg_replace('!\s+!', '', $_POST["team_id"]);
        $color = preg_replace("/\s+/", ";", strtolower($_POST["color"]??""));
        $size = preg_replace("/\s+/", ";", strtolower((isset($_POST["size"]) && !preg_match('/^\s*$/', $_POST["size"]))? $_POST["size"] : PRODUCTS_DEFAULT_SIZE ));

        // Image handling
        $img_url = ["", ""];
        $img_url[0] = $_POST["img_url_1"]? preg_replace('/\s+/', ' ', $_POST["img_url_1"]):"";
        $index = !($img_url[0] === "");
        $img_url[$index] = $_POST["img_url_2"]? preg_replace('/\s+/', ' ', $_POST["img_url_2"]):"";

        // Alt handling
        $alts = ["", ""];
        $alts[0] = $_POST["alt_1"]? preg_replace('/\s+/', ' ', $_POST["alt_1"]):"";
        $index = !($alts[0] === "");
        $alts[$index] = $_POST["alt_2"]? preg_replace('/\s+/', ' ', $_POST["alt_2"]):"";

        // No image urls provided, so "images-local" should be taken into account
        $s3_upload = 0;
        if ($img_url[0] == "" && $img_url[1] == "" && isset($_FILES["images-local"]) && $_FILES["images-local"] && $_FILES["images-local"]["name"][0]) {

            // Make sure to look for empty file names and paths, the array might contain empty strings. Use array_filter() before count.
            $files = array_filter($_FILES['images-local']['name']);
            $total = count($_FILES["images-local"]["name"]);
            if ($total > MAX_NUMBER_FILES) {
                error("-1", "Max number of files exceeded.", "\controllers\store\\edit.php", "/f1-webapp/views/private/store/edit.php?id=$id");
                exit;
            }

            $file_temp_src = $_FILES["images-local"]["tmp_name"];
            foreach ($_FILES["images-local"]["name"] as $index => $filename) {
                [$status, $statusMsg, $s3_file_link] = aws_s3_upload($filename, $file_temp_src[$index]);
                if ($status == "danger") {
                    error("-1", "AWS S3: $statusMsg.", "\controllers\store\\edit.php", "/f1-webapp/views/private/store/edit.php?id=$id");
                    exit;
                }
                // Save the uploaded image url in img_url array
                $img_url[$index] = $s3_file_link;
                $s3_upload = 1;
            }
        }

        /* -- ERROR | Empty input fields -- */
        if ($id == "" || $id == " "
            || $title == "" || $title == " "
            || $price == "" || $price == " "
            || $team_id == "" || $team_id == " ") {
            error("-1", "Empty input fields.", "\controllers\store\\edit.php", "/f1-webapp/views/private/store/edit.php?id=$id");
            exit;
        }

        // REGEX PRICE xx.yy
        if (!preg_match("/^\d+([,.]\d{1,2})?$/", $price)) {
            error("-1", "Price NOT valid.", "\controllers\store\\edit.php", "/f1-webapp/views/private/store/edit.php?id=$id");
            exit;
        }
        $price = preg_replace("/,/", ".", $price);


        /* DB */
        $conn = DB::connect("\controllers\store\\edit.php", "/f1-webapp/views/private/store/edit.php?id=$id");
        $price = number_format($price, 2) * 100;
        $img_url_str = implode("\t", $img_url);
        $alt_str = implode("\t", $alts);
        $team_id = intval($team_id);

        /* CHECK INPUT LENGTHS */
        // $input_array = [$title, $desc, $price, $img_url, $team_id, $color, $size, $alts];
        foreach ([$id, $title, $desc, $price, $img_url_str, $team_id, $color, $size, $alt_str] as $index => $input) {
            if (PRODUCTS_MAX_LENGTHS[$index] >= 0 && strlen($input) > PRODUCTS_MAX_LENGTHS[$index]) {
                $tmp = ucfirst(PRODUCTS_ARRAY[$index]);
                error("500", "$tmp is TOO long.", "\controllers\store\\edit.php", "/f1-webapp/views/private/store/edit.php?id=$id");
                exit;
            }
        }

        DB::p_stmt_no_select($conn,
            "UPDATE Products SET title=?, description=?, price=?, img_url=?, team_id=?, color=?, size=?, alt=? WHERE id = ?",
            ["s", "s", "i", "s", "i", "s", "s", "s", "i"],
            [$title, $desc, $price, $img_url_str, $team_id, $color, $size, $alt_str, $id],
            "\controllers\store\\update.php",
            "/f1-webapp/views/private/store/update.php?id=$id");

        if (!$conn->close()) {
            error("500", "conn_close()", "\controllers\store\\edit.php", "/f1-webapp/views/private/store/edit.php?id=$id");
            exit;
        }

        $_SESSION["success"] = 1;
        $_SESSION["success_msg"] = "Product updated successfully";
        header("Location: /f1-webapp/views/private/store/all.php");

    } else {
        error("500", "Fields not provided.", "\controllers\store\\edit.php", "/f1-webapp/views/private/store/all.php");
    }
} else {
    $_SESSION['redirection'] = "/f1-webapp/controllers/store/update.php?id=${${$_POST['id']??''}}";
    error("401", "Unauthorised access!", "\controllers\store\\edit.php", "/f1-webapp/views/public/auth/login.php");
}
exit;