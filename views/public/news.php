<?php

/** https://getcomposer.org/doc/00-intro.md */
/** https://copyprogramming.com/howto/php-php-get-image-by-xpath?utm_content=cmp-true */

if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once ("controllers/news/news.php");
require_once ("views/partials/public/news_cards.php");
require_once("controllers/auth/auth.php");

const COL_CARD = "col-12 col-sm-6 col-lg-4 col-xl-3";

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>News</title>
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
            <span class="title text-light">
                <span class="text-light h2">
                    News
                    <span class="material-symbols-outlined text-danger">download</span>
                </span>
                (provided by <a href="<?php echo BASE_URL; ?>" target="_blank" class="text-info text-decoration-none">
                    <?php echo $loadFromDisk ? "backup" : "formula1.com"; ?>
                </a>)
            </span>
            <?php if (count($title_list) > 0) {
                echo_news_cards($title_list, $img_list, $link_list, MAX_NEWS_NEWS, COL_CARD);
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
</body>
</html>