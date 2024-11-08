<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("controllers/auth/auth.php");
require_once("utility/utility_func.php");
require_once("views/partials/alert.php");
require_once("utility/store.php");

if (isset($_GET["search"]) && $_GET["search"]!=""){
    $search = $_GET["search"];
    $conn = DB::connect("\\views\public\search.php", "/f1-webapp/views/public/index.php");
    [$num_products, $products] = DB::stmt_get_record_by_field(
        $conn,
        " SELECT Products.id AS 'Products.id', Products.title AS 'Products.title', Products.color AS 'Products.color', Products.size AS 'Products.size', Products.description AS 'Products.description', Products.price AS 'Products.price', Products.img_url AS 'Products.img_url' 
                FROM products 
                WHERE Products.title LIKE '%$search%' or Products.description LIKE '%$search%' or Products.color LIKE '%$search%' or Products.size LIKE '%$search%' ");

    if (!$conn->close()) {
        error("500", "conn_close()", "store.php", "/f1-webapp/views/public/index.php");
        exit;
    }
}

else{
    header("location: /f1-webapp/views/public/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Result</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/index_style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/store/store.css">
</head>

<body class="bg-dark">
<div class="container-fluid">

    <!-- Nav -->
    <?php include("views/partials/navbar_store.php") ?>

    <!-- Message alert div -->
    <?php err_msg_alert(); ?>

    <!-- Loading circle -->
    <?php include("views/partials/loading.php"); ?>

    <h1 class="d-flex justify-content-center text-align-center">STORE</h1>
    <main class="home-cards mt-5">
        <?php include("views/partials/store/view_products.php") ?>
    </main>

<?php include ("views/partials/footer.php"); ?>
<script src="/f1-webapp/assets/js/navbar.js"></script>
<script src="/f1-webapp/assets/js/store/store.js"></script>
</body>
</html>
