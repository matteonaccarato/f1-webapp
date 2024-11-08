<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once ("controllers/statistics/statistics.php");
require_once ("views/partials/public/statistics_cards.php");
require_once("controllers/auth/auth.php");
const START_YEAR = 1949;
const COL_CARD = "col-12 col-sm-6 col-lg-4 col-xl-3";

$year = date("Y");
if(isset($_GET["year"]))
    $year = $_GET["year"];

$BASE_URL_STATISTICS = "https://www.formula1.com/en/results.html/" . $year ."/races.html";
define("BACKUP_FILE", $_SERVER['DOCUMENT_ROOT'] . "\\DB\backup\\statistics" . $year . ".json");

$lists = f1_scrape_stat($BASE_URL_STATISTICS);
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
[$races, $dates, $winners, $teams, $laps, $times, $links] = $lists;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Statistics</title>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/info_cards.css">
    <?php include("views/partials/head.php"); ?>
</head>

<body class="bg-dark">
<div class="container-fluid">

    <!-- Nav -->
    <?php include ("views/partials/navbar.php");?>

    <main>
        <br>
        <div class="d-flex justify-content-between align-items-center">

            <div class="title text-light">
                <span class="text-light h2 d-flex justify-content-start align-items-center">
                    <button type="button" id="teams" class="navigate-left navigate btn col-2 col-sm-2 col-md-1 d-flex justify-content-center hover-red switch-page"><span class="material-symbols-outlined">chevron_left</span></button>
                    <span class="left_element"><?php echo date("Y"); ?> Teams</span>
                </span>
            </div>


            <div class="title text-light d-flex justify-content-center">
                <div class="d-flex flex-column align-items-center">
                    <div class="text-light margin h2 d-flex justify-content-start align-items-center">
                        <span class="central_element"><?php  isset($_GET["year"])? print $_GET["year"]: print date("Y") ?> Statistics</span>
                    </div>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Select Year</a>
                        <ul style="max-height: 280px; overflow-y: auto" class="dropdown-menu">
                            <?php for($k = date("Y"); $k > START_YEAR; --$k){ ?>
                            <li><a class="dropdown-item" href="/f1-webapp/views/public/statistics.php?year=<?php echo $k; ?>"><?php echo $k; ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="title text-light">
                <span class="text-light h2 d-flex justify-content-end align-items-center">
                    <span class="right_element"><?php echo date("Y"); ?> Circuits</span>
                    <button type="button" id="circuits" class="navigate-left navigate btn col-2 col-sm-2 col-md-1 d-flex justify-content-center hover-red switch-page"><span class="material-symbols-outlined">chevron_right</span></button>
                </span>
            </div>
        </div>
        <?php if ($races && count($races) > 0) {
            echo_stat_cards($races, $dates, $winners, $teams, $laps, $times, $links, COL_CARD);
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




