<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once("views/partials/alert.php");
require_once ("utility/store.php");
require_once ("DB/DB.php");


/* GET TEAMS */
$conn = DB::connect("\\views\public\store\store.php", "/f1-webapp/views/public/index.php");
[$num_teams, $teams] = DB::stmt_get_record_by_field($conn,
    "SELECT * FROM Teams;",
    "\\views\public\store\store.php",
    "/f1-webapp/views/public/index.php");

/* GET Products (eventually filtered by team) */
$team_filter = (isset($_GET["team"]) && $_GET["team"])? ("WHERE team_id = " . $_GET["team"]):"";
[$num_products, $products] = DB::stmt_get_record_by_field($conn,
    "SELECT 
                Products.id AS 'Products.id', Products.title AS 'Products.title', Products.color AS 'Products.color', Products.size AS 'Products.size', Products.description AS 'Products.description', Products.price AS 'Products.price', Products.img_url AS 'Products.img_url', Products.alt AS 'Products.alt', 
                Teams.id AS 'Teams.id', Teams.name AS 'Teams.name', Teams.color_rgb_value AS 'Teams.color_rgb_value' 
            FROM Products JOIN Teams ON Products.team_id = Teams.id 
            $team_filter 
            ORDER BY Products.id DESC;",
    "\\views\public\store\store.php",
    "/f1-webapp/views/public/index.php");
if (!$conn->close()) {
    error("500", "conn_close()", "\\views\public\store\store.php", "/f1-webapp/views/public/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Store</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/index_style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/store/store.css">
</head>

<body class="bg-dark">
<div class="container-fluid">

    <!-- Nav -->
    <?php include ("views/partials/navbar_store.php")?>

    <!-- Filter by team -->
    <div class="w-100 d-flex flex-column gap-3">
        <h3 class="d-flex justify-content-center">
            Shop by Team
        </h3>
        <div id="shop-by-team" class="row d-flex justify-content-center align-items-center gap-5 p-3">
            <?php foreach($teams as $team) { ?>
                <a href="?team=<?php echo htmlentities($team["id"]); ?>">
                    <img src="<?php echo htmlentities($team["logo_url"]); ?>" alt="<?php echo htmlentities($team["name"]) . " Logo"; ?>">
                </a>
            <?php } ?>
        </div>
    </div>

    <?php err_msg_alert(); ?>

    <!-- Loading circle -->
    <?php include ("views/partials/loading.php"); ?>

    <!-- Actual Products list -->
    <main class="home-cards mt-5">

        <?php include("views/partials/store/view_products.php") ?>

        <div class="page-selector d-flex justify-content-center align-items-center gap-3 py-5">
            <button class="btn btn-navigate-page d-flex justify-content-center align-items-center" id="prev-page">
                <span class="material-symbols-outlined text-danger">fast_rewind</span>
            </button>
            <button class="btn btn-outline-danger btn-navigate-page" id="curr-page">1</button>
            <button class="btn btn-navigate-page d-flex justify-content-center align-items-center" id="next-page">
                <span class="material-symbols-outlined text-danger">fast_forward</span>
            </button>
        </div>
    </main>
</div>
<?php include ("views/partials/footer.php"); ?>
<script src="/f1-webapp/assets/js/navbar.js"></script>
<script src="/f1-webapp/assets/js/store/store.js"></script>
</body>
</html>