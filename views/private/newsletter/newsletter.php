<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once ("controllers/auth/auth.php");
require_once ("views/partials/alert.php");

[$login_allowed, $user] = check_cookie();
if (!check_admin_auth($user)) {
    $_SESSION['redirection'] = "/f1-webapp/views/private/newsletter/newsletter.php";
    error("401", "not_authorized", "\\views\private\\newsletter\\newsletter.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Newsletter</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/loading.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/admin/newsletter.css">
    <script src="/f1-webapp/assets/js/newsletter.js" defer></script>
</head>
<body class="bg-dark">
<div class="container-fluid">

    <?php include("views/partials/navbar.php");?>

    <main>
        <div class="container-fluid vh-100 d-flex justify-content-center">
            <div class="container-fluid container-element px-3">

                <?php err_msg_alert(); ?>
                <?php succ_msg_alert(); ?>

                <form id="newsletter" action="/f1-webapp/controllers/newsletter/send_email.php" method="post">
                        <span class="d-flex justify-content-center">
                            <label for="subject" class="form-label d-flex gap-2">
                                <span class="material-symbols-outlined text-danger">mail</span>
                                Subject
                            </label>
                        </span>
                    <div class="input-group">
                        <textarea class="form-control text-box" id="subject" name="subject" rows="1" cols="50"></textarea>
                    </div>
                    <br>
                    <span class="d-flex justify-content-center">
                            <label for="text" class="form-label d-flex gap-2">
                                <span class="material-symbols-outlined text-danger">mail</span>
                                Body
                            </label>
                        </span>
                    <div class="input-group">
                        <textarea class="form-control text-box" id="text" name="text" rows="5" cols="50"></textarea>
                    </div>
                    <div class="row mb-3 d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 py-5">
                        <button id="btn_submit" type="submit" class="btn btn-danger col-6 d-flex align-items-center justify-content-center gap-2 btn-reverse-color">
                            <span class="material-symbols-outlined">send</span>
                            <span id="send-txt">Send</span>
                        </button>
                        <div class="d-none lds-ring-container">
                            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include ("views/partials/footer.php"); ?>
</body>
</html>