<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once ("controllers/statistics/statistics.php");
require_once ("views/partials/public/statistics_cards.php");
require_once("controllers/auth/auth.php");

const COL_CARD = "col-12 col-sm-6 col-lg-4 col-xl-3";

$BASE_URL_STATISTICS = "https://www.formula1.com/en/results.html/" . date("Y") ."/races.html";
if(isset($_GET["year"])){
    $url = "https://www.formula1.com/en/results.html/".$_GET["year"]."/races.html";
}
else
    $url = $BASE_URL_STATISTICS;

[$info, $date, $car, $laps] = f1_scrape_stat($url);
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
                            <?php for($k = date("Y"); $k > 1949; --$k){ ?>
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
        <?php if (count($info) > 0) {
            echo_stat_cards($info, $date, $car, $laps, COL_CARD);
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




