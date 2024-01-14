<!-- TODO: LocalStorage.clear() -->
<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once ("utility/store.php");
require_once ("utility/utility_func.php");
require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("controllers/auth/auth.php");

[$login_allowed, $user] = check_cookie();
if ($login_allowed) {

    if (isset($_POST["ids"]) && isset($_POST["titles"]) /* && isset($_POST["teams"]) */ && isset($_POST["quantities"]) &&  isset($_POST["imgs"]) && isset($_POST["prices"]) && isset($_POST["sizes"]) && isset($_POST["total"]) && isset($_POST["address"])) {

        /* CLEANING INPUT */
        $ids = $_POST["ids"];
        $titles = $_POST["titles"];
        $teams = $_POST["teams"];
        $quantities = $_POST["quantities"];
        $imgs = $_POST["imgs"];
        $prices = $_POST["prices"];
        $sizes = $_POST["sizes"];
        $total = $_POST["total"];
        $address = preg_replace('/\s+/', ' ', $_POST["address"]);

        // Check input values
        if ($address == '' || $address == ' ') {
            error("500", "Address is empty.", "\controller\orders\create.php", "/f1_project/views/public/store/cart.php");
            exit;
        }

        /* DB */
        $conn = DB::connect("\controller\orders\create.php", "/f1_project/views/public/store/cart.php");

        try {
            $order_id = generate_random_string(5);

            $products_id_array = explode("\t", $ids);
            $quantities_array = explode("\t", $quantities);
            $prices_array = explode("\t", $prices);
            $sizes_array = explode("\t", $sizes);
            $imgs_array = explode("\t", $imgs);
            $titles_array = explode("\t", $titles);
            // $teams_array = explode("\t", $teams);

            // Check input (e.g. input not valid if ids="5 null 3"; title => "null null t-shirt")
            for ($i = 0; $i < count($products_id_array) - 1; $i++) {
                if ($products_id_array[$i] == null || preg_match('/^\s*$/', $products_id_array[$i])
                || $sizes_array[$i] == null || preg_match('/^\s*$/', $sizes_array[$i])
                || $quantities_array[$i] == null || preg_match('/^\s*$/', $quantities_array[$i])
                || $prices_array[$i] == null || preg_match('/^\s*$/', $prices_array[$i])
                // || $imgs_array[$i] == null || preg_match('/^\s*$/', $imgs_array[$i])
                || $titles_array[$i] == null || preg_match('/^\s*$/', $titles_array[$i])) {
                    error("500", "Some fields are empty.", "\controller\orders\create.php", "/f1_project/views/public/store/cart.php");
                    exit;
                }
            }

            // Create order in DB
            DB::p_stmt_no_select($conn,
                "INSERT INTO orders VALUES (?, ?, ?, ?, ?);",
                ["s", "i", "s", "s", "i"],
                [$order_id, $user["Users.id"], (new DateTime())->format('Y-m-d H:i:s'), $address, $total],
                "\controller\orders\create.php",
                "/f1_project/views/public/store/cart.php");

            for ($i = 0; $i < count($products_id_array) - 1; $i++) {

                // Create relation order-products
                // If a product in the order is not available anymore, the order will be deleted through the use of the last parameter ($order_id)
                DB::p_stmt_no_select($conn,
                    "INSERT INTO orders_products VALUES (?, ?, ?, ?, ?);",
                    ["s", "i", "s", "i", "i"],
                    [$order_id, $products_id_array[$i], $sizes_array[$i], $quantities_array[$i], $prices_array[$i]],
                    "\controller\orders\create.php",
                    "/f1_project/views/public/store/cart.php",
                    $order_id);
            }

            $subject = "Ready to goooo! You've just completed your order :)";
            $body = "";
            for ($i = 0; $i < count($products_id_array) - 1; $i++) {
                $size = strtoupper($sizes_array[$i]);
                [$int, $dec] = str2int_dec($prices_array[$i]);
                $body .= "$titles_array[$i]<br>";
                $body .= "Size: $size<br>";
                $body .= "[$quantities_array[$i]x <strong>$int.$dec &euro;</strong>]<br>";
                $body .= "<img src='$imgs_array[$i]' width='65px;' alt='product picture'>";
                $body .= "<hr>";
            }

            [$int, $dec] = str2int_dec($total);
            $body .= "Total: <strong>$int.$dec &euro;</strong>";
            $body .= "<hr>";
            $body .= "Address: $address";
            send_mail([$user["Users.email"]], $subject, $body);

            if (!$conn->close()) {
                error("500", "conn_close()", "\controller\orders\create.php", "/f1_project/views/public/store/cart.php");
                exit;
            }

            $_SESSION["success"] = 1;
            $_SESSION["success_msg"] = "Order created successfully";
            header("Location: /f1_project/views/public/store/cart.php");

        } catch (Exception $e) {
            error("500", "Exception: $e", "\controller\orders\create.php", "/f1_project/views/public/store/cart.php");
        }


    } else {
        error("500", "Fields not provided.", "\controller\orders\create.php", "/f1_project/views/public/store/cart.php");
    }
} else {
    $_SESSION['redirection'] = "/f1_project/controllers/orders/create.php";
    error("401", "Unauthorised access!", "\controller\orders\create.php", "/f1_project/views/public/auth/login.php");
}
exit;