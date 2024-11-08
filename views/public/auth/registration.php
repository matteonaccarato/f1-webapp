<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("views/partials/alert.php");

[$login_allowed, $user] = check_cookie();
if (check_user_auth($user)) {
    set_session($user);
    if (check_admin_auth($user)) {
        header("Location: /f1-webapp/views/private/users/all.php");
    } else {
        $_SESSION["err"] = 1;
        $_SESSION["err_msg"] = "Not allowed operation.";
        header("Location: /f1-webapp/views/public/index.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign Up</title>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/log_reg_style.css">

    <?php if (isset($_SESSION["confirm_email"]) && !$_SESSION["confirm_email"]){ ?>
        <link rel="stylesheet" href="/f1-webapp/assets/css/text_confirm_email.css">
    <?php } ?>

    <?php include("views/partials/head.php"); ?>
</head>


<body class="bg-dark vh-100">

<div id="bg-register" class="container-fluid d-flex justify-content-center align-items-center">

    <?php include("views/partials/navbar_log_reg.php"); ?>

    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <?php
        if (isset($_SESSION["confirm_email"]) && !$_SESSION["confirm_email"]){?>
            <div class="container-element text">
                <h1>Request received</h1>
                <br>
                <h3>Check your mailbox and confirm your email.</h3>
                <h3>Once confirmed, refresh this page and log in</h3>
            </div>
        <?php }
        else if (isset($_SESSION["confirm_email"]) && $_SESSION["confirm_email"]){
            unset($_SESSION["confirm_email"]);
            header("location: /f1-webapp/views/public/auth/login.php");
            exit;
        }
        else{ ?>
            <form id="register-form" action="/f1-webapp/controllers/auth/registration.php" method="POST" class="container-fluid container-element registration col-sm-6">

                <div style="margin-left: 10px; margin-right: 10px">
                    <?php err_msg_alert(); ?>
                </div>

                <fieldset>
                    <legend class="d-flex align-items-center justify-content-start gap-2 hover-red">
                        <span class="material-symbols-outlined">passkey</span>
                        <b>REGISTER</b>
                    </legend>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <label for="firstname" class="form-label text-box"><strong>FIRST NAME</strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark text-box" id="firstname-addon">badge</span>
                                <input type="text" id="firstname" name="firstname" class="form-control" placeholder="Your first name" aria-describedby="firstname-addon" required>
                            </div>
                            <div id="input-info-firstname" class="d-none d-flex gap-2 mt-1 py-1 text-box">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="lastname" class="form-label text-box"><strong>LAST NAME</strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark text-box" id="lastname-addon">badge</span>
                                <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Your last name" required>
                            </div>
                            <div id="input-info-lastname" class="d-none d-flex gap-2 mt-1 py-1 text-box">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="email" class="form-label text-box"><strong>EMAIL</strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark text-box" id="email-addon">mail</span>
                                <input type="email" id="email" class="form-control" name="email"  placeholder="name@example.com" aria-describedby="email-addon" required>
                            </div>
                            <div class="text-box d-flex gap-2 mt-1 py-1">
                                <span id="status_symbol" style="display: none" class="material-symbols-outlined text-danger">warning</span>
                                <span id="status" class="text-danger"></span>
                            </div>
                            <div class="form-text mx-1 text-light"><p class="text-box"> your <strong>Top secret</strong> email :P </p></div>
                            <div id="input-info-email" class="d-none d-flex gap-2 mt-1 py-1 text-box">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <label for="pass" class="form-label text-box"><strong>PASSWORD</strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark text-box" id="pass-addon">lock</span>
                                <input type="password" class="form-control" id="pass" name="pass" placeholder="Password" required>
                            </div>
                            <div id="input-info-pass" class="d-none d-flex gap-2 mt-1 py-1 text-box">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="pass-confirm" class="form-label text-box"><strong>REPEAT PASSWORD</strong></label><br>
                            <div class="input-group">
                                <span class="input-group-text material-symbols-outlined text-dark text-box" id="pass-confirm-addon">lock</span>
                                <input type="password" class="form-control" id="pass-confirm" name="confirm" placeholder="Repeat password">
                            </div>
                            <div id="input-info-pass-confirm" class="d-none d-flex gap-2 mt-1 py-1 text-box">
                                <span class="material-symbols-outlined"></span>
                                <span class=""></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-sm-6 mb-3 mb-sm-0">
                            <label for="date_of_birth" class="form-label text-box"><strong>DATE OF BIRTH</strong></label>
                            <div class="input-group">
                                <input id="date_of_birth" name="date_of_birth" class="form-control text-box" type="date" />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mb-3 mb-sm-0 d-flex align-items-end">
                            <div class="d-flex flex-row gap-1">
                                <input class="form-check-input text-box" type="checkbox" id="newsletter" name="newsletter">
                                <label class="form-check-label mx-1 d-flex align-items-center justify-content-start gap-2" for="newsletter" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" title="Receive <strong>exclusive</strong> contents :P">
                                    Subscribe to newsletter
                                    <span class="material-symbols-outlined" style="color: aqua; margin-right: 12px">help</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div id="div_button" class="row col-12 d-flex justify-content-center align-items-center mx-1 gap-3">
                        <button type="submit" id="btn-submit" class="btn-submit btn btn-danger col-6 col-sm-6 col-md-5 d-flex align-items-center justify-content-center gap-1">
                            <span class="material-symbols-outlined">person_add</span>
                            <strong>Sign up</strong>
                        </button>
                        <a href="/f1-webapp/views/public/auth/login.php" class="my_outline_animation col-12 col-sm-3 text-center text-white text-decoration-none d-flex align-items-center justify-content-center gap-1 p-2 hover-red">
                            <span class="material-symbols-outlined">login</span>
                            <span class="d-inline d-xxl-inline">Login</span>
                        </a>
                    </div>
                </fieldset>
            </form>
            <?php
        }
        ?>
    </div>
</div>

<script src="/f1-webapp/assets/js/validators/user.js"></script>
<script src="/f1-webapp/assets/js/tooltip.js"></script>
</body>
</html>