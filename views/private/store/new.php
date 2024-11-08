<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once ("DB/DB.php");
require_once("views/partials/alert.php");

[$login_allowed, $user] = check_cookie();
if (!check_admin_auth($user)) {
    $_SESSION['redirection'] = "/f1-webapp/views/private/store/new.php";
    error("401", "not_authorized", "\\views\private\store\\new.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
set_session($user);

$conn = DB::connect("\\views\private\store\\new.php", "/f1-webapp/views/private/users/all.php");
[$num_teams, $teams] = DB::stmt_get_record_by_field($conn,
    "SELECT * FROM Teams;",
    "\\views\private\store\\new.php",
    "/f1-webapp/views/private/users/all.php");

if (!$conn->close()) {
    error("500", "conn_close()", "\\views\private\store\\new.php", "/f1-webapp/views/private/users/all.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin | Product</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>
    <?php include ("views/partials/toggle_head.php"); ?>

    <!--modificare la navbar-->
    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/index_style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/admin/product_new.css">
</head>

<body class="vh-100 bg-dark">

    <?php include("views/partials/navbar.php") ?>

    <div class="container-fluid row d-flex flex-row justify-content-center align-items-center gap-3 mt-5">
        <div class="col-12 col-md-10">

            <form action="/f1-webapp/controllers/store/create.php" enctype="multipart/form-data" id="form-loading" class="container col-12 col-xl-6 py-3 border border-3 border-danger rounded" method="POST">

                <?php err_msg_alert(); ?>
                <?php succ_msg_alert(); ?>

                <fieldset>
                    <legend class="d-flex align-items-center justify-content-start gap-2 hover-red">
                        <span class="material-symbols-outlined">add</span>
                        <strong>NEW</strong>
                    </legend>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="title" class="form-label"><strong>TITLE <span class="text-danger">*</span></strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark" id="title-addon">title</span>
                                <input type="text" id="title" class="form-control" name="title" placeholder="Title" aria-describedby="title-addon" required>
                            </div>
                            <div id="input-info-title" class="d-none d-flex gap-2 mt-1 py-1">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="desc" class="form-label"><strong>DESCRIPTION</strong></label>
                            <textarea class="form-control" id="desc" name="desc" rows="2" placeholder="Describe the product"></textarea>
                            <div id="input-info-desc" class="d-none d-flex gap-2 mt-1 py-1">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="price" class="form-label"><strong>PRICE <span class="text-danger">*</span></strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark" id="price-addon">euro</span>
                                <input type="text" id="price" class="form-control" name="price" pattern="^[0-9]+([,.][0-9]{1,2})?$" placeholder="50.00" aria-describedby="price-addon" required>
                            </div>
                            <div id="input-info-price" class="d-none d-flex gap-2 mt-1 py-1">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="team_id" class="form-label"><strong>TEAM <span class="text-danger">*</span></strong></label>
                            <select name="team_id" id="team_id" class="form-select rounded" aria-label="Select team" required>
                                <option value="" class="option_invalid" selected disabled>Select team</option>
                                <?php
                                foreach ($teams as $team) {
                                    echo "<option value=" . htmlentities($team["id"]) . " class='option_valid'>" . htmlentities($team["name"]) . "</option>";
                                }
                                ?>
                            </select>
                            <div id="input-info-team_id" class="d-none d-flex gap-2 mt-1 py-1">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label style="width: fit-content" class="form-label mx-1 d-flex align-items-center justify-content-start gap-2" for="size" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" title="<strong>Blank space</strong> to separate<br>(eg. XS S M L XL)">
                                <strong>SIZE</strong>
                                <span class="material-symbols-outlined" style="color: aqua;">help</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark" id="size-addon">sell</span>
                                <input type="text" id="size" class="form-control" name="size" placeholder="XS S M" aria-describedby="size-addon">
                            </div>
                            <div id="input-info-size" class="d-none d-flex gap-2 mt-1 py-1">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label style="width: fit-content" class="form-label mx-1 d-flex align-items-center justify-content-start gap-2" for="color" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" title="<strong>Blank space</strong> to separate<br>(eg. Red Orange Black)">
                                <strong>COLOR</strong>
                                <span class="material-symbols-outlined" style="color: aqua;">help</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark" id="color-addon">palette</span>
                                <input type="text" id="color" class="form-control" name="color" placeholder="red black" aria-describedby="color-addon">
                            </div>
                            <div id="input-info-color" class="d-none d-flex gap-2 mt-1 py-1">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                    </div>

                    <!-- Local Images -->
                    <div class="row mb-4 d-none" id="image-local-div">
                        <div class="col-12">
                            <label for="images-local" class="form-label"><strong>UPLOAD IMAGE(S)</strong></label>
                            <input class="form-control" type="file" accept=".jpg,.jpeg,.png" id="images-local" name="images-local[]" multiple>
                        </div>
                        <div id="input-info-images-local" class="d-none d-flex gap-2 mt-1 py-1">
                            <span class="material-symbols-outlined"></span>
                            <span class=""></span>
                        </div>
                    </div>

                    <!-- Web Images -->
                    <div class="row mb-4" id="image-url-div">
                        <div class="col-6">
                            <label for="img_url_1" class="form-label"><strong>IMAGE (1)</strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark" id="img-1-addon">image</span>
                                <input type="text" id="img_url_1" class="img_url form-control" name="img_url_1" placeholder="https://image.url" aria-describedby="img-1-addon">
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="img_url_2" class="form-label"><strong>IMAGE (2)</strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark" id="img-2-addon">image</span>
                                <input type="text" id="img_url_2" class="img_url form-control" name="img_url_2" placeholder="https://image.url" aria-describedby="img-2-addon">
                            </div>
                        </div>
                        <div id="input-info-images" class="d-none d-flex gap-2 mt-1 py-1">
                            <span class="material-symbols-outlined"></span>
                            <span class=""></span>
                        </div>
                    </div>

                    <!-- ALT images input -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <label for="alt_1" class="form-label"><strong>ALT (1)</strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark" id="alt-1-addon">description</span>
                                <input type="text" id="alt_1" class="form-control" name="alt_1" placeholder="Alt description for the 1st image" aria-describedby="alt-1-addon">
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="alt_2" class="form-label"><strong>ALT (2)</strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark" id="alt-2-addon">description</span>
                                <input type="text" id="alt_2" class="form-control" name="alt_2" placeholder="Alt description for the 2nd image" aria-describedby="alt-2-addon">
                            </div>
                        </div>
                        <div id="input-info-alts" class="d-none d-flex gap-2 mt-1 py-1">
                            <span class="material-symbols-outlined"></span>
                            <span class=""></span>
                        </div>
                    </div>

                    <!-- Image upload methodology toggle -->
                    <div class="row mb-3">
                        <div class="col-12 d-flex justify-content-start align-items-center gap-2">
                            <label class="checkbox-inline" for="choose-file-upload"></label>
                            <input type="checkbox" id="choose-file-upload" data-toggle="toggle" data-on="Local" data-off="URL" data-onstyle="danger" data-offstyle="border border-danger">
                        </div>
                    </div>

                    <hr>

                    <div class="row col-12 d-flex justify-content-end align-items-center mx-1 mb-3 gap-3">

                        <!-- Loading circle -->
                        <?php include ("views/partials/loading.php"); ?>

                        <button type="submit" id="btn-submit" class="btn-reverse-color btn btn-danger col-12 col-sm-6 col-md-5 d-flex align-items-center justify-content-center gap-2">
                            <span class="material-symbols-outlined">add</span>
                            <strong>Create</strong>
                        </button>
                        <a href="/f1-webapp/views/private/store/all.php" class="my_outline_animation col-12 col-sm-3 text-center text-white text-decoration-none d-flex align-items-center justify-content-center gap-1 p-2 hover-red">
                            <span class="material-symbols-outlined">fast_rewind</span>
                            <span class="d-inline d-sm-none d-xxl-inline">Discard</span>
                        </a>
                    </div>
                    <div class="row col-12">
                        <label>
                            <span class="text-danger">*</span> Compulsory fields
                        </label>
                    </div>
                </fieldset>
            </form>
        </div>

        <div id="img-preview" class="d-none col-12 row d-flex justify-content-center">
            <img id="img-url-1" class="d-none img-url col-12 col-sm-6 mb-3 mb-sm-0 rounded" alt="" src="#">
            <img id="img-url-2" class="d-none img-url col-12 col-sm-6 rounded" alt="" src="#">
        </div>
    </div>

    <?php include ("views/partials/footer.php"); ?>
<script src="/f1-webapp/assets/js/validators/products.js"></script>
<script src="https://benalman.com/code/projects/jquery-throttle-debounce/jquery.ba-throttle-debounce.js"></script>
<script src="/f1-webapp/assets/js/store/crud.js"></script>
<script src="/f1-webapp/assets/js/loading-crud.js"></script>
<script src="/f1-webapp/assets/js/image_upload.js"></script>
<script src="/f1-webapp/assets/js/tooltip.js"></script>

</body>
</html>