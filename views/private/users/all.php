<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");
if (session_status() == PHP_SESSION_NONE) session_start();

require_once("controllers/auth/auth.php");
require_once("utility/error_handling.php");
require_once("DB/DB.php");
require_once("views/partials/alert.php");

[$login_allowed, $user] = check_cookie();
if (!check_admin_auth($user)) {
    $_SESSION['redirection'] = "/f1-webapp/views/private/users/all.php";
    error("401", "not_authorized", "\\views\private\users\all.php", "/f1-webapp/views/public/auth/login.php", "Unauthorized access.");
    exit;
}
set_session($user);

$conn = DB::connect("\\views\private\users\all.php", "/f1-webapp/views/private/dashboard.php");
[$num_users, $users] = DB::stmt_get_record_by_field($conn,
    "SELECT * FROM Users;",
    "\\views\private\users\all.php",
    "/f1-webapp/views/private/dashboard.php");

if (!$conn->close()) {
    error("500", "conn_close()", "\\views\private\users\all.php", "/f1-webapp/views/private/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Users</title>
    <meta charset="UTF-8">

    <?php include("views/partials/head.php"); ?>

    <link rel="stylesheet" href="/f1-webapp/assets/css/style.css">
    <link rel="stylesheet" href="/f1-webapp/assets/css/admin/table_style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script> $(document).ready( function () { $('#table').DataTable(); }); </script>
</head>

<body class="vh-100 dark">
    <div class="container-fluid">

        <?php include("views/partials/navbar.php") ?>

        <div class="flex-container d-flex flex-column justify-content-center align-items-center mt-5">
            <div class="container-element col-12 col-md-9">

                <!-- Loading circle -->
                <?php include("views/partials/loading.php"); ?>

                <?php if ($num_users > 0) { ?>

                    <?php succ_msg_alert(); ?>
                    <?php err_msg_alert(); ?>

                    <table id="table" class="display">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">FIRST NAME</th>
                                <th class="text-center">LAST NAME</th>
                                <th class="text-center">E-MAIL</th>
                                <th class="text-center fit-content col-2">DATE OF BIRTH</th>
                                <th class="text-center col-1">ROLE</th>
                                <th class="text-center col-1">NEWSLETTER</th>
                                <th class="text-center col-1">IMG</th>
                                <th class="text-center col-1">REMOVE</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $correspondence_vector = ["first_name", "last_name", "email", "date_of_birth", "role", "newsletter"];
                        foreach ($users as $user) { ?>
                            <tr>
                                <th class='text-center'>
                                    <a href='/f1-webapp/views/private/users/show_profile.php/?id=<?php echo htmlentities($user["id"]); ?>' class="text-decoration-none" style="color: #4a82fc">
                                        <?php echo htmlentities($user["id"]); ?>
                                    </a>
                                </th>

                                <?php for($i = 0;$i < 6; ++$i){?>
                                <td class='text-center'>
                                    <?php echo htmlentities($user[$correspondence_vector[$i]]); ?>
                                </td>
                                <?php } ?>

                                <td class='text-center'>
                                    <?php if($user["img_url"] != ''){ ?>
                                        <img style="width: 60px; height: 40px; object-fit: cover;" src="<?php echo htmlentities($user['img_url']); ?>" alt="<?php echo ($user["alt"] !== "")? htmlentities($user["alt"]):"Profile picture"; ?>">
                                    <?php
                                    }
                                    else{ ?>
                                        <span class='material-symbols-outlined'>close</span>
                                    <?php
                                    } ?>
                                </td>

                                <td class='text-center delete-loading'>
                                    <a href='/f1-webapp/controllers/users/delete.php/?id=<?php echo htmlentities($user["id"]); ?>' class='my-auto d-flex align-items-center justify-content-center text-decoration-none'>
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
            </div>
        </div>
    </div>

    <?php include ("views/partials/footer.php"); ?>
<script src="/f1-webapp/assets/js/loading-crud.js"></script>
</body>
</html>