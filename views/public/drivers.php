<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/drivers/drivers.php");
require_once("controllers/drivers/info_drivers.php");
require_once ("views/partials/public/drivers_cards.php");
require_once("controllers/auth/auth.php");

const COL_CARD = "col-12 col-sm-6 col-lg-4 col-xl-3";
define("BACKUP_FILE", $_SERVER['DOCUMENT_ROOT'] . "\\DB\backup\\drivers\\drivers.json");
$info = ["Team", "Country", "Podiums", "Points", "Grands Prix entered", "World Championships",
    "Highest race finish", "Highest grid position", "Date of birth", "Place of birth"];

$lists = f1_scrape_drivers(BASE_URL);

$loadFromDisk = 0;
foreach ($lists as $el) {
    if (count($el) == 0)
        $loadFromDisk = 1;
}
// Load/Store from json
if ($loadFromDisk)
    $lists = json_decode(file_get_contents(BACKUP_FILE));
else
    file_put_contents(BACKUP_FILE, json_encode($lists));

[$name_list, $team_list, $flag_list, $number_list, $img_list, $url_list] = $lists;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Drivers</title>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/info_cards.css">
    <?php include("views/partials/head.php"); ?>
</head>

<body class="bg-dark">
<script src="/f1-webapp/assets/js/info_drivers.js"></script>
<div class="container-fluid">

    <!-- Nav -->
    <?php include ("views/partials/navbar.php");?>

    <main>
        <br>
        <div class="d-flex justify-content-between align-items-center">

            <div class="title text-light">
                <span class="text-light h2 d-flex justify-content-start align-items-center">
                    <button type="button" id="circuits" class="navigate-left navigate btn col-2 col-sm-2 col-md-1 d-flex justify-content-center hover-red switch-page"><span class="material-symbols-outlined">chevron_left</span></button>
                    <span class="left_element"><?php echo date("Y"); ?> Circuits</span>
                </span>
            </div>

            <div class="text-light margin h2 d-flex justify-content-start align-items-center">
                <span class="central_element"><?php echo date("Y"); ?> Drivers</span>
            </div>

            <div class="title text-light">
                <span class="text-light h2 d-flex justify-content-end align-items-center">
                    <span class="right_element"><?php echo date("Y"); ?> Teams</span>
                    <button type="button" id="teams" class="navigate-left navigate btn col-2 col-sm-2 col-md-1 d-flex justify-content-center hover-red switch-page"><span class="material-symbols-outlined">chevron_right</span></button>
                </span>
            </div>

        </div>
        <?php if (count($name_list) > 0) {
            echo_drivers_cards($name_list, $team_list, $flag_list, $number_list, $img_list, $url_list,COL_CARD);
        } else { ?>
            <div class="alert border-light text-dark fade show d-flex align-items-center justify-content-center mt-4 col-12" role="alert">
                <span class="material-symbols-outlined">description</span>
                <span class="mx-2">
                    <b>INFO</b>&nbsp;| No Data available!
                </span>
            </div>
        <?php } ?>
    </main>
</div>

<?php include ("views/partials/footer.php"); ?>

<script src="/f1-webapp/assets/js/navigate.js"></script>
</body>
</html>


