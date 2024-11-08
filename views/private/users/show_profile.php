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
<html lang="en" id="h">
<head>
    <title>User detail</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/private/profile_style.css">
</head>

<body class="bg-dark">

<?php
[$login_allowed, $user] = check_cookie();
if (check_user_auth($user)) {
    set_session($user);

    if (check_admin_auth($user)) {
        (isset($_GET["id"]) && $_GET["id"] != null) ? $id = $_GET["id"] : $id = null;
        $element = select_user($id);
        unset($id);
    } else {
        $element = select_user(null);
    }
    if($element != null) { ?>
        <div class="container-fluid" >
            <?php include("views/partials/navbar.php"); ?>
            <main >
                <div id="bg-profile" class="flex-container d-flex justify-content-center" >
                    <div id="profile-data" class="container-element">
                        <div style="margin-left: 10px; margin-right: 10px">
                            <?php err_msg_alert(); ?>
                            <?php succ_msg_alert(); ?>
                        </div>
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
                                        <label class="form-label"><strong class=" text-red">Firstname</strong></label><br>
                                    </span>
                                    <div class="input-group d-flex text-center">
                                        <label class="form-control text-box" ><?php ($element["first_name"] != "")?print htmlentities($element["first_name"]): print "No element";?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="col-12">
                            <span class="d-flex justify-content-center">
                                <label class="form-label"><strong class="text-red">Lastname</strong></label><br>
                            </span>
                                    <div class="input-group d-flex text-center">
                                        <label class="form-control text-box" ><?php ($element["last_name"] != "")?print htmlentities($element["last_name"]): print "No element"; ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                        <span class="d-flex justify-content-center">
                            <label class="form-label"><strong class="text-red">Email</strong></label><br>
                        </span>
                                <div class="input-group d-flex text-center">
                                    <label class="form-control text-box"><?php ($element["email"] != "")?print htmlentities($element["email"]): print "No element"; ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <div class="col-12">
                            <span class="d-flex justify-content-center">
                                <label class="form-label"><strong class="text-red">Date of birth</strong></label><br>
                            </span>
                                    <div class="input-group d-flex text-center">
                                        <label class="form-control text-box" ><?php ($element["date_of_birth"] != "")?print htmlentities($element["date_of_birth"]): print "No element"; ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="col-12">
                            <span class="d-flex justify-content-center">
                                <label class="form-label"><strong class="text-red">Remember me</strong></label><br>
                            </span>
                                    <div class="input-group d-flex text-center">
                                        <label class="form-control text-box" ><?php ($element["cookie_id"] != null)? print "Active": print "Not Active" ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div id="button_div" class="row mb-3 d-flex justify-content-center button">
                            <button id="viewProfile" type="button" class="btn btn-danger col-6 col-sm-6 col-md-5 d-flex align-items-center justify-content-center">Edit profile</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <script>
            $(`#viewProfile`).on("click", () => {
                window.open("/f1-webapp/controllers/users/edit.php/?id=<?php echo htmlentities($element["id"]); ?>","_self");
            })
        </script>

<?php
    } else{
        error("500", "User is NULL", "\\views\users\show_profile.php", "/f1-webapp/views/private/dashboard.php");
    }
}
else{
    $_SESSION['redirection'] = "/f1-webapp/views/private/users/show_profile.php?id={${${$_GET['id']??''}}}";
    error("401", "not_authorized", "\\views\users\show_profile.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
?>
</body>
</html>