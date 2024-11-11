<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once ("controllers/news/news.php");
require_once ("views/partials/public/news_cards.php");
require_once("controllers/auth/auth.php");
require_once ("views/partials/alert.php");

const COL_CARD = "col-12";

define("BACKUP_FILE", $_SERVER['DOCUMENT_ROOT'] . "\\DB\backup\\news.json");
$lists = f1_scrape_news(BASE_URL);
$loadFromDisk = 0;
foreach ($lists as $el) {
    if (count($el) == 0)
        $loadFromDisk = 1;
}
// Load/Store from json
if ($loadFromDisk && file_exists(BACKUP_FILE))
    $lists = json_decode(file_get_contents(BACKUP_FILE));
else
    file_put_contents(BACKUP_FILE, json_encode($lists));

[$title_list, $img_list, $link_list] = $lists;

$json = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "\\views\\partials\\public\\index_cards.json");
$json_cards_data = json_decode($json, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/index_style.css">
</head>

<body class="bg-dark">
<div class="container-fluid">

    <!-- Nav -->
    <?php include ("views/partials/navbar.php");?>

    <main>
        <?php err_msg_alert(); ?>
        <?php succ_msg_alert(); ?>

        <!-- Showcase -->
        <div id="Indicators" class="carousel slide " data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#Indicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#Indicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#Indicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <?php
                foreach (["stroll-canada-2.jpg", "alonso-canada.jpg", "austin.jpg"] as $index => $pic) { ?>
                    <div class="carousel-item <?php echo $index == 0 ? "active" : "" ?>">
                        <img src="<?php echo "/f1-webapp/assets/images/" . $pic ?>" class="d-block w-100 img-carousel rounded" alt="F1 car and track">
                    </div>
                <?php } ?>


            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#Indicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#Indicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <br>

        <!-- Home cards -->
        <section class="home-cards row d-flex justify-content-around gap-5 gap-md-0">

            <!-- News -->
            <div style="border-radius: 10px; background: rgba(87, 87, 87, .6); " class="col-12 order-2 col-md-4 order-md-1 d-flex justify-content-center flex-column">
                <span class="title text-light pt-2">
                    <span class="text-light h2">
                        News
                        <span class="material-symbols-outlined text-danger">download</span>
                    </span>
                    (provided by <a href="<?php echo BASE_URL; ?>" target="_blank" class="text-info text-decoration-none">
                        <?php echo $loadFromDisk ? "backup" : "formula1.com"; ?>
                    </a>)
                </span>
                <?php echo_news_cards($title_list, $img_list, $link_list, MAX_NEWS_INDEX, COL_CARD); ?>
            </div>

            <!-- Browse our site -->
            <div class="col-12 col-md-8">
                <h2 class="title text-light d-flex justify-content-center align-items-center gap-2">Browse our site
                    <span class="material-symbols-outlined text-danger">travel_explore</span>
                </h2>
                <br>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-4">

                    <?php foreach ($json_cards_data as $card) { ?>
                        <div class="col d-flex align-items-stretch">
                            <div class="card border border-danger border-3 p-2 d-flex flex-column justify-content-between">
                                <div class="card-img">
                                    <img src="<?php echo htmlentities($card["img_url"]); ?>" class="card-img-top" alt="<?php echo htmlentities($card["alt"]); ?>">
                                </div>
                                <div class="card-body d-flex align-items-end">
                                    <div class="w-100">
                                        <h5 class="card-title text-danger"><?php echo htmlentities($card["title"]); ?></h5>
                                        <hr>
                                        <p class="card-text"><?php echo $card["text"]; ?></p>
                                        <p class="card-text">
                                            <a href="<?php echo htmlentities($card["link"]); ?>" class="card-link text-decoration-none d-flex flex-row justify-content-end">
                                                <span class="my_outline_animation d-flex flex-row gap-2 pb-1 hover-red">
                                                    <span class="material-symbols-outlined">keyboard_double_arrow_right</span>
                                                    Check it out!
                                                    <span class="material-symbols-outlined">sports_score</span>
                                                </span>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="mt-5 who-we-are">
                    <h2>Who we are
                        <span class="material-symbols-outlined text-danger">sports_martial_arts</span>
                    </h2>
                    <hr>
                    <div>
                        F1 INFO was born from two passionates about Formula 1.<br>
                        We designed our website to perform different actions with it.<br>
                        You can (virtually) <a href="/f1-webapp/views/public/store/store.php" class="h5">BUY</a> products, keep yourself updated with the most recent <a href="/f1-webapp/views/public/news.php" class="h5">NEWS</a>
                        and, also, navigate in various <a href="/f1-webapp/views/public/statistics.php" class="h5">STATISTICS</a> (from 1950 to <?php echo date("Y"); ?>).
                    </div>
                </div>

            </div>

        </section>
    </main>
</div>
<?php include ("views/partials/footer.php"); ?>
</body>
</html>