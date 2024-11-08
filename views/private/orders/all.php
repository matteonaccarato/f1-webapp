<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once ("utility/store.php");
require_once ("DB/DB.php");
require_once("views/partials/alert.php") ;

[$login_allowed, $user] = check_cookie();
if (!check_user_auth($user)) {
    $_SESSION['redirection'] = "/f1-webapp/views/private/orders/all.php";
    error("401", "not_authorized", "\\views\private\orders\all.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
set_session($user);

$conn = DB::connect("\\views\private\store\all.php", "/f1-webapp/views/private/dashboard.php");
$orders = (array)DB::get_record_by_field($conn,
    "SELECT orders.id AS 'Orders.id', orders.date AS 'Orders.date', orders.amount AS 'Orders.amount', 
                products.id AS 'Products.id', products.title AS 'Products.title', products.img_url AS 'Products.img_url', products.alt AS 'Products.alt',
                orders_products.size AS 'Orders_Products.size', orders_products.quantity AS 'Orders_Products.quantity', orders_products.unit_price AS 'Orders_Products.unit_price'
           FROM orders_products 
                JOIN orders ON orders_products.order_id = orders.id 
                JOIN products ON orders_products.product_id = products.id
           WHERE orders.user_id = ?
           ORDER BY orders.date DESC;",
    ["i"],
    [$user["Users.id"]??$_SESSION["id"]],
    "\\views\private\orders\all.php",
    "/f1-webapp/views/private/dashboard.php");
$num_orders = count($orders);

if (!$conn->close()) {
    error("500", "conn_close()", "\\views\private\orders\all.php", "/f1-webapp/views/private/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User | Orders</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/admin/table_style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css">

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script> $(document).ready( function () { $('#table').DataTable({ order: [[5, 'desc'] ]}); }); </script>
</head>

<body class="vh-100 dark">
    <div class="container-fluid">

        <?php include("views/partials/navbar.php") ?>

        <div class="flex-container d-flex flex-column justify-content-center align-items-center mt-3 mt-md-5">
            <div class="container-element col-12 col-md-9">
                <div class="mb-4 mb-md-5 ">
                    <h2 class="text-start">Your orders</h2>
                    <span>(same <strong>#order</strong> means the items are part of the same order)</span>
                </div>

                <?php if ($num_orders > 0) { ?>

                    <?php succ_msg_alert(); ?>
                    <?php err_msg_alert(); ?>

                    <!-- Loading circle -->
                    <?php include ("views/partials/loading.php"); ?>

                    <table id="table" class="display">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">PRODUCT</th>
                            <th class="text-center">SIZE</th>
                            <th class="text-center">QUANTITY</th>
                            <th class="text-center fit-content col-2">IMGs</th>
                            <th class="text-center">DATE</th>
                            <th class="text-center">AMOUNT</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $orders_id = [];

                        $i = 0;
                        foreach ($orders as $order) {
                            if (!in_array($order["Orders.id"], $orders_id)) {
                                $i++;
                                $orders_id[] = $order["Orders.id"];
                            } ?>
                            <tr>
                                <th class='text-center'>
                                    <?php echo htmlentities($order["Orders.id"]); ?>
                                </th>
                                <td class="text-center">
                                    <a href="/f1-webapp/views/public/store/product.php?id=<?php echo htmlentities($order["Products.id"]); ?>" target="_blank" class="text-decoration-none"><?php echo $order["Products.title"] ?></a>
                                </td>
                                <td class="text-center">
                                    <?php echo htmlentities(strtoupper($order["Orders_Products.size"])); ?>
                                </td>
                                <td class="text-center">
                                    <?php echo htmlentities($order["Orders_Products.quantity"]); ?>
                                </td>
                                <td class="text-center">
                                    <?php if($order["Products.img_url"] != ''){ ?>
                                        <img style="width: 60px; height: 40px; object-fit: contain;" src="<?php echo htmlentities($order["Products.img_url"]); ?>" alt="<?php echo htmlentities(($order["Products.alt"] && $order["Products.alt"] !== "")?$order["Products.alt"]:$order["Products.title"]); ?>">
                                    <?php
                                    } else { ?>
                                        <span class='material-symbols-outlined'>close</span>
                                    <?php } ?>
                                </td>
                                <td class='text-center'>
                                    <?php echo htmlentities($order["Orders.date"]); ?>
                                </td>
                                <td class='text-center'>
                                    <?php [$int, $dec] = str2int_dec($order["Orders_Products.unit_price"]  * $order["Orders_Products.quantity"]); ?>
                                    <strong><?php echo htmlentities($int . "." . $dec); ?></strong> €
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert border-light text-dark fade show d-flex align-items-center justify-content-center mt-4 col-12" role="alert">
                        <span class="material-symbols-outlined">description</span>
                        <span class="mx-2">
                            <b>INFO</b>&nbsp;| No Data available!
                        </span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

<?php include ("views/partials/footer.php"); ?>
<script src="/f1-webapp/assets/js/store/order.js"></script>
</body>
</html>