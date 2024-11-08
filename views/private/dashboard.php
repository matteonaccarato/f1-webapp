<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once ("DB/DB.php");
require_once ("views/partials/alert.php");

[$login_allowed, $user] = check_cookie();
if(!check_user_auth($user)){
    $_SESSION['redirection'] = "/f1-webapp/views/private/dashboard.php";
    error("401", "not_authorized", "dashboard.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <?php include("views/partials/head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/private/dashboard_style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
</head>
<style>

</style>
<body class="bg-dark">
    <div class="container-fluid">
        <?php include ("views/partials/navbar.php"); ?>
        <main>
            <?php err_msg_alert(); ?>
            <?php succ_msg_alert(); ?>
            <div class="flex-container">
                <div class="flex-slide profile">
                    <div class="flex-title flex-title-profile"><span class="material-symbols-outlined full-size-icon">person</span></div>
                    <div id="goProfile" class="flex-about">
                        <p class="text-center">View | Edit</p></div>
                </div>

                <?php if (check_admin_auth($user)) { ?>
                    <div class="flex-slide table-users">
                        <div class="flex-title">
                            <span class="material-symbols-outlined full-size-icon">admin_panel_settings</span>
                        </div>
                        <div id="goTable" class="flex-about mb-2">
                            <p class="text-center">
                                <span class="material-symbols-outlined full-size-icon">group</span>
                                Users
                            </p>
                        </div>
                        <div id="goStore" class="flex-about mt-2">
                            <p class="text-center">
                                <span class="material-symbols-outlined full-size-icon">store</span>
                                Store
                            </p>
                        </div>
                    </div>
                <?php } ?>

                <div class="flex-slide product">
                    <div class="flex-title flex-title-product"><span class="material-symbols-outlined full-size-icon">shopping_bag</span></div>
                    <div id="goOrders" class="flex-about"><p class="text-center">Orders</p></div>
                </div>
            </div>
        </main>
    </div>

<script src="/f1-webapp/assets/js/dashboard_script.js"></script>
</body>
</html>