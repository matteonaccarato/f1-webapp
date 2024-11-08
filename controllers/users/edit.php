<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("views/partials/alert.php");
require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once("utility/utility_func.php");
require_once("DB/DB.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User detail</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>
    <?php include("views/partials/toggle_head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/private/profile_style.css">
</head>

<body class="bg-dark">

<?php
[$login_allowed, $user] = check_cookie();
if (check_user_auth($user)) {
    set_session($user);

    if(check_admin_auth($user)) {
        (isset($_GET["id"]) && $_GET["id"] != null) ? $id = $_GET["id"] : $id = null;
        $element = select_user($id);
        unset($id);
    } else {
        $element = select_user(null);
    }
    $_SESSION['redirection'] = "/f1-webapp/views/private/users/show_profile.php?id=" . $element['id'];
    if($element != null) { ?>
        <div class="container-fluid">
            <?php include("views/partials/navbar.php"); ?>
            <main>
                <div id="bg-profile" class="flex-container d-flex justify-content-center ">
                    <form id="profile-data" class="container-element" enctype="multipart/form-data" method="POST" action="/f1-webapp/controllers/users/update.php">
                        <div id="page1">
                            <div class="d-flex justify-content-center">
                                <?php if ($element["img_url"] != null && $element["img_url"] != "") { ?>
                                    <img class="photo_profile rounded-circle" src="<?php echo htmlentities($element['img_url']); ?>"
                                         alt="<?php echo ($element["first_name"]? htmlentities($element["first_name"]):"") . " Profile picture"; ?>">
                                <?php } else { ?>
                                    <img class="photo_profile rounded-circle" src="/f1-webapp/assets/images/default_img_profile.jpeg" alt="Standard profile picture. Abstract design of the upper part of a human body with a question mark inside the head.">
                                <?php } ?>
                            </div>
                            <br>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="col-12">
                                        <span class="d-flex justify-content-center">
                                            <label for="firstname" class="form-label"><strong class="text-red">Firstname <span>*</span></strong></label><br>
                                        </span>
                                        <div class="input-group">
                                            <input type="text" id="firstname" name="firstname" class="form-control text-box" placeholder="First name" value="<?php echo htmlentities($element["first_name"]); ?>" required>
                                        </div>
                                        <div id="input-info-firstname" class="d-none text-box d-flex gap-2 mt-1 py-1">
                                            <span class="material-symbols-outlined"></span>
                                            <span class=""></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="col-12">
                                <span class="d-flex justify-content-center">
                                    <label for="lastname" class="form-label"><strong class="text-red">Lastname <span>*</span></strong></label><br>
                                </span>
                                        <div class="input-group d-flex text-center">
                                            <input type="text" id="lastname" name="lastname" class="form-control text-box" placeholder="Last name" value="<?php echo htmlentities($element["last_name"]); ?>" required>
                                        </div>
                                        <div id="input-info-lastname" class="d-none text-box d-flex gap-2 mt-1 py-1">
                                            <span class="material-symbols-outlined"></span>
                                            <span class=""></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <span class="d-flex justify-content-center">
                                        <label for="email" class="form-label"><strong class="text-red">Email <span>*</span></strong></label><br>
                                    </span>
                                    <div class="input-group d-flex text-center">
                                        <?php if($_SESSION["role"] == 1 && $_SESSION["id"] != $element["id"]){ ?>
                                            <label for="original-email" class="form-control text-box"><?php echo htmlentities($element["email"]); ?></label>
                                            <input type="email" id="email" name="email"  class="d-none form-control text-box" placeholder="name@example.com" value="<?php echo htmlentities($element["email"]); ?>" required>
                                        <?php } else {?>
                                            <input type="email" id="email" name="email"  class="form-control text-box rounded" placeholder="name@example.com" value="<?php echo htmlentities($element["email"]); ?>" required>
                                        <?php } ?>
                                        <input type="email" id="original-email" class="d-none" value="<?php echo htmlentities($element["email"]); ?>" required>

                                    </div>

                                    <div class="text-box d-flex gap-2">
                                        <span id="status_symbol" style="display: none" class="material-symbols-outlined text-danger">warning</span>
                                        <span id="status" class="text-danger"></span>
                                    </div>
                                    <div id="input-info-email" class="d-none text-box d-flex gap-2 mt-1 py-1">
                                        <span class="material-symbols-outlined"></span>
                                        <span class=""></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">

                                <div class="col-12 col-md-6">
                                    <span class="d-flex justify-content-center">
                                        <label for="edit_date_of_birth" class="form-label"><strong class="text-red">Date of birth</strong></label><br>
                                    </span>
                                    <div class="input-group d-flex text-center">
                                        <?php if($_SESSION["role"] == 1 && $_SESSION["id"] != $element["id"]) { ?>
                                            <label class="form-control text-box"><?php ($element["date_of_birth"] != null)? print $element["date_of_birth"]: print "No element"; ?></label>
                                        <?php } else {?>
                                            <input type="date" id="edit_date_of_birth" name="edit_date_of_birth" class="form-control text-box" value="<?php echo htmlentities($element["date_of_birth"]); ?>">
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <span class="d-flex justify-content-center">
                                        <label for="edit_news" class="form-label"><strong class="text-red">Newsletter</strong></label><br>
                                    </span>
                                    <div class="input-group d-flex text-center">
                                        <?php if($_SESSION["role"] == 1 && $_SESSION["id"] != $element["id"]) { ?>
                                            <label class="form-control text-box"><?php ($element["newsletter"] != null)? print $element["newsletter"]: print "No element"; ?></label>
                                        <?php } else {?>
                                            <input type="number" id="edit_news" name="edit_news" class="form-control text-box" placeholder="Active: 1  Inactive: 0" value="<?php echo htmlentities($element["newsletter"]); ?>">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="row mb-3 d-flex justify-content-center button button_div">
                                <button type="button" name="other_info_back" class="navigate-left navigate btn btn-outline-light col-2 col-sm-2 col-md-1 d-flex justify-content-center n-border"><span class="material-symbols-outlined">chevron_left</span></button>
                                <button type="submit" id="btn-submit" name="id_to_update" value="<?php echo htmlentities($_GET["id"]); ?>" class="btn-submit btn btn-danger col-6 col-sm-6 col-md-5 d-flex align-items-center justify-content-center">Confirm</button>
                                <button id="goToNextPage" type="button" name="other_info_next" class="navigate-right navigate btn btn-outline-light col-2 col-sm-2 col-md-1 d-flex justify-content-center n-border">
                                  <span class="material-symbols-outlined">chevron_right</span>
                                </button>
                            </div>
                        </div>

                        <div id="page2" class="d-none">
                            <div class="d-flex justify-content-center">
                                <?php if ($element["img_url"] != null && $element["img_url"] != "") { ?>
                                    <img class="photo_profile rounded-circle" src="<?php echo htmlentities($element['img_url']); ?>"
                                         alt="<?php echo ($element["first_name"]? htmlentities($element["first_name"]):"") . " Profile picture"; ?>">
                                <?php } else { ?>
                                    <img class="photo_profile rounded-circle" src="/f1-webapp/assets/images/default_img_profile.jpeg" alt="Standard profile picture. Abstract design of the upper part of a human body with a question mark inside the head.">
                                <?php }?>
                            </div>
                            <br>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="col-12">
                                        <span class="d-flex justify-content-center">
                                            <label for="pass" class="form-label"><strong class="text-red">Password</strong></label><br>
                                        </span>
                                        <div class="input-group d-flex text-center">
                                            <?php if($_SESSION["role"] == 1 && $_SESSION["id"] != $element["id"]) { ?>
                                                <label class="form-control text-box">Not allowed</label>
                                            <?php } else {?>
                                                <input type="password" id="pass" name="pass" class="form-control text-box" placeholder="Password">
                                            <?php } ?>
                                        </div>
                                        <div id="input-info-pass" class="d-none text-box d-flex gap-2 mt-1 py-1">
                                            <span class="material-symbols-outlined"></span>
                                            <span class=""></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="col-12">
                                        <span class="d-flex justify-content-center">
                                            <label for="pass-confirm" class="form-label"><strong class="text-red">Confirm password</strong></label><br>
                                        </span>
                                        <div class="input-group d-flex text-center">
                                            <?php if($_SESSION["role"] == 1 && $_SESSION["id"] != $element["id"]) { ?>
                                                <label class="form-control text-box">Not allowed</label>
                                            <?php } else {?>
                                                <input type="password" id="pass-confirm" name="pass_confirm" class="form-control text-box" placeholder="Confirm password">
                                            <?php } ?>
                                        </div>
                                        <div id="input-info-pass-confirm" class="d-none text-box d-flex gap-2 mt-1 py-1">
                                            <span class="material-symbols-outlined"></span>
                                            <span class=""></span>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div id="upload-img" class="row mb-3 d-flex justify-content-center align-items-center">

                                <!-- LOCAL UPLOAD IMAGE -->
                                <div class="row mb-3 d-none col-9" id="image-local-div">
                                    <div class="col-12">
                                        <span class="d-flex justify-content-center">
                                            <label for="image-local" class="form-label"><strong class="text-red">Upload image</strong></label><br>
                                        </span>
                                        <input class="form-control" type="file" accept=".jpg,.jpeg,.png" id="image-local" name="image-local">

                                        <div id="input-info-image-local" class="d-none d-flex gap-2 mt-1 py-1">
                                            <span class="material-symbols-outlined"></span>
                                            <span class=""></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- WEB UPLOAD IMAGE -->
                                <div class="row mb-3 col-9" id="image-url-div">
                                    <div class="col-12">
                                        <span class="d-flex justify-content-center">
                                            <label for="img_url_1" class="form-label"><strong class="text-red">Url img</strong></label><br>
                                        </span>
                                        <div class="input-group d-flex text-center">
                                            <input type="text" id="img_url_1" name="edit_img" class="form-control text-box" placeholder="https//image.url" value="<?php echo htmlentities($element["img_url"]); ?>">
                                        </div>
                                        <div id="input-info-img_url" class="d-none text-box d-flex gap-2 mt-1 py-1">
                                            <span class="material-symbols-outlined"></span>
                                            <span class=""></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image upload methodology toggle -->
                                <div id="checkbox_div" class="col-3 d-flex justify-content-center">
                                    <label class="checkbox-inline" for="choose-file-upload"></label>
                                    <input type="checkbox" id="choose-file-upload" data-toggle="toggle" data-on="Local" data-off="URL" data-onstyle="danger" data-offstyle="border border-danger">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                <span class="d-flex justify-content-center">
                                    <label class="form-label"><strong class="text-red">Remember me</strong></label><br>
                                </span>
                                    <div class="input-group d-flex text-center">
                                        <label class="form-control text-box"><?php ($element["cookie_id"] != null)? print "Active": print "Inactive"; ?></label>
                                    </div>
                                </div>
                                <div class="col 12 col-md-6">
                            <span class="d-flex justify-content-center">
                                <label <?php if($_SESSION["role"] == 1 && $_SESSION["id"] != $element["id"]){?> for="edit_role" <?php } ?> class="form-label"><strong class=" text-red">Role</strong></label><br>
                            </span>
                                    <div class="input-group d-flex text-center">
                                        <?php if($_SESSION["role"] == 1 && $_SESSION["id"] != $element["id"]){?>
                                            <input type="number" id="edit_role" name="edit_role" class="form-control text-box" placeholder="Admin: 1 / User: 0" value="<?php echo htmlentities($element["role"]); ?>">
                                        <?php } else{ ?>
                                            <label class="form-control text-box"> <?php ($_SESSION["role"] == 1)? print "Admin": print "User"; ?> </label>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="row mb-3 d-flex justify-content-center button button_div">
                                <button id="goToLastPage" type="button" name="other_info" class="navigate-left navigate btn btn-outline-light col-2 col-sm-2 col-md-1 d-flex justify-content-center n-border"><span class="material-symbols-outlined">chevron_left</span></button>
                                <button type="submit" name="id_to_update" value="<?php echo htmlentities($element["id"]); ?>" class="btn-submit btn btn-danger col-6 col-sm-6 col-md-5 d-flex align-items-center justify-content-center">Confirm</button>
                                <button type="button" name="other_info" class="navigate-right btn navigate btn-outline-light col-2 col-sm-2 col-md-1 d-flex justify-content-center n-border"><span class="material-symbols-outlined">chevron_right</span></button>
                            </div>
                        </div>

                        <!-- Loading circle -->
                        <?php include("views/partials/loading.php"); ?>
                    </form>
                </div>
            </main>
        </div>

        <script src="/f1-webapp/assets/js/users/edit.js"></script>
        <script src="/f1-webapp/assets/js/validators/user.js"></script>
        <script src="/f1-webapp/assets/js/image_upload.js"></script>

<?php
    } else{
        error("500", "User is NULL", "\\views\users\show_profile.php", "/f1-webapp/views/private/dashboard.php");
    }
} else{
    $_SESSION['redirection'] = "/f1-webapp/views/private/users/show_profile.php?id={${${$_GET['id']??''}}}";
    error("401", "not_authorized", "\\views\users\show_profile.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
?>
</body>
</html>
