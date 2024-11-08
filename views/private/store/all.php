<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if(session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once ("utility/store.php");
require_once ("DB/DB.php");
require_once("views/partials/alert.php");

[$login_allowed, $user] = check_cookie();
if (!check_admin_auth($user)) {
    $_SESSION['redirection'] = "/f1-webapp/views/private/store/all.php";
    error("401", "not_authorized", "\\views\private\store\all.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
set_session($user);

$conn = DB::connect("\\views\private\store\all.php", "/f1-webapp/views/private/dashboard.php");
[$num_products, $products] = DB::stmt_get_record_by_field($conn,
    "SELECT Products.id AS 'Products.id', Products.title AS 'Products.title', Products.price AS 'Products.price', Products.img_url AS 'Products.img_url', Products.alt AS 'Products.alt', 
                Teams.name AS 'Teams.name' 
            FROM Products JOIN Teams ON Products.team_id = Teams.id
            ORDER BY Products.id DESC;",
    "\\views\private\store\all.php",
    "/f1-webapp/views/private/dashboard.php");

if (!$conn->close()) {
    error("500", "conn_close()", "\\views\private\store\all.php", "/f1-webapp/views/private/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Store</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/admin/table_style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css">

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script> $(document).ready( function () { $('#table').DataTable({ order: [[0, 'desc'] ]}); }); </script>
</head>

<body class="vh-100 dark">
    <div class="container-fluid">

        <?php include("views/partials/navbar.php") ?>

        <div class="flex-container d-flex flex-column justify-content-center align-items-center mt-5">

            <div class="container-element col-12 col-md-9">

                <?php if ($num_products > 0) { ?>

                    <?php succ_msg_alert(); ?>
                    <?php err_msg_alert(); ?>

                    <table id="table" class="display">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">TITLE</th>
                                <th class="text-center">TEAM</th>
                                <th class="text-center">PRICE</th>
                                <th class="text-center fit-content col-2">IMGs</th>
                                <th class="text-center col-1">REMOVE</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $correspondence_vector = ["Products.title", "Teams.name"];
                        foreach ($products as $product) { ?>
                            <tr>
                                <th class='text-center'>
                                    <a href='edit.php/?id=<?php echo htmlentities($product["Products.id"]); ?>' class="text-decoration-none" style="color: #4a82fc">
                                        <?php echo htmlentities($product["Products.id"]); ?>
                                    </a>
                                </th>

                                <?php for($i = 0;$i < count($correspondence_vector); $i++){?>
                                    <td class='text-center'>
                                        <?php echo htmlentities((strlen($product[$correspondence_vector[$i]]) < 70)? $product[$correspondence_vector[$i]] : (substr($product[$correspondence_vector[$i]], 0 , 70) . " [...]")); ?>
                                    </td>
                                <?php } ?>

                                <td class="text-center">
                                    <?php [$int, $dec] = str2int_dec($product["Products.price"]); ?>
                                    € <?php echo htmlentities($int . "." . $dec); ?>
                                </td>

                                <td class='text-center' >
                                    <?php
                                    if ($product["Products.img_url"]) {
                                        $img = explode("\t", $product["Products.img_url"]);
                                        $alt = explode("\t", $product["Products.alt"]);
                                        if($img && $img[0] != '') { ?>
                                            <img style="width: 60px; height: 40px; object-fit: contain;" src="<?php echo htmlentities($img[0]); ?>" alt="<?php echo htmlentities(($alt[0] !== "")?$alt[0]:$product["Products.title"]); ?>">
                                        <?php } else { ?>
                                            <span class='material-symbols-outlined'>close</span>
                                        <?php }
                                        if($img && $img[1] != '') { ?>
                                            <img style="width: 60px; height: 40px; object-fit: contain;" src="<?php echo htmlentities($img[1]); ?>" alt="<?php echo htmlentities(($alt[1] && $alt[1] !== "")?$alt[1]:$product["Products.title"]); ?>">
                                        <?php
                                        }
                                    } else { ?>
                                        <span class='material-symbols-outlined'>close</span>
                                    <?php }
                                    ?>
                                </td>

                                <td class='text-center delete-loading'>
                                    <a href='/f1-webapp/controllers/store/delete.php/?id=<?php echo htmlentities($product["Products.id"]); ?>' class='my-auto d-flex align-items-center justify-content-center text-decoration-none'>
                                        <span class='material-icons text-danger'>delete</span>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        } ?>
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

                <div class="d-flex justify-content-end py-3">
                    <form method="get" action="/f1-webapp/views/private/store/new.php" class="text-decoration-none">
                        <button class="btn btn-reverse-color rounded btn btn-danger d-flex justify-content-center align-items-center gap-2">
                            <span class="material-symbols-outlined">add</span>
                            <span>Create</span>
                        </button>
                    </form>
                </div>

                <!-- Loading circle -->
                <?php include ("views/partials/loading.php"); ?>
            </div>
        </div>
    </div>

<?php include ("views/partials/footer.php"); ?>
<script src="/f1-webapp/assets/js/loading-crud.js"></script>
</body>
</html>