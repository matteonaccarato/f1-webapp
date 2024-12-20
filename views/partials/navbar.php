<nav id="navbar" class="w-100 navbar navbar-expand-lg mb-3 mb-lg-0">
    <div class="container-fluid px-4">
        <a id="navbar-logo" class="navbar-brand px-5" href="/f1-webapp/views/public/index.php"></a>

        <button class="navbar-toggler text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="material-symbols-outlined">menu</span>
        </button>

        <div class="collapse navbar-collapse d-lg-flex justify-content-lg-between align-items-center" id="navbarNav">
            <form class="d-none d-lg-flex" method="GET" role="search" action="/f1-webapp/views/public/search/search.php" name="search_bar">
                <input class="form-control me-2" type="search" name="search" id="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-danger" type="submit">Search</button>
            </form>

            <div class="navbar-nav d-flex flex-column align-items-end gap-4 flex-lg-row justify-content-lg-end gap-lg-5">

                <?php include("views/partials/public/news_store_btn.php"); ?>

                <div class="d-flex gap-3">
                    <?php
                    [$login_allowed, $user] = check_cookie();
                    if (!check_user_auth($user)) {?>
                        <div class="nav-item">
                            <a class="nav-link btn btn-danger text-light px-4 d-flex gap-2" href="/f1-webapp/views/public/auth/login.php">
                                <span class="material-symbols-outlined">login</span>
                                <span>Login</span>
                            </a>
                        </div>
                        <div class="nav-item my_outline_animation">
                            <a class="nav-link text-light d-flex gap-2" href="/f1-webapp/views/public/auth/registration.php">
                                <span class="material-symbols-outlined">how_to_reg</span>
                                <span>Registration</span>
                            </a>
                        </div>
                    <?php } else {
                        set_session($user);?>
                        <div class="nav-item">
                            <a class="my_outline_animation text-decoration-none hover-red d-flex align-items-end pb-1 gap-2" href="/f1-webapp/views/private/dashboard.php">

                                <?php if(isset($_SESSION["img_url"]) && $_SESSION["img_url"] != NULL){ ?>
                                    <img class="profile-img" style="object-fit: cover;" src="<?php echo htmlentities($_SESSION["img_url"]); ?>" alt="<?php echo ($_SESSION["first_name"]? htmlentities($_SESSION["first_name"]):"") . " Profile picture"; ?>">
                                <?php } else{ ?>
                                    <img class="profile-img" style="object-fit: cover" src="/f1-webapp/assets/images/default_img_profile.jpeg" alt="Standard profile picture. Abstract design of the upper part of a human body with a question mark inside the head.">
                                <?php }?>

                                <span>Dashboard</span>
                            </a>
                        </div>
                        <?php if(check_admin_auth($user)){ ?>
                            <div class="nav-item">
                                <a class="my_outline_animation text-decoration-none hover-red d-flex align-items-end pb-1 gap-2" href="/f1-webapp/views/private/newsletter/newsletter.php">
                                    <span class="material-symbols-outlined">mail</span>
                                    <span>Newsletter</span>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="nav-item">
                            <a class="my_outline_animation text-decoration-none hover-red d-flex align-items-end pb-1 gap-2" href="/f1-webapp/controllers/auth/logout.php">
                                <span class="material-symbols-outlined">logout</span>
                                <span>Logout</span>
                            </a>
                        </div>
                    <?php } ?>
                </div>
                <form class="d-flex flex-row d-lg-none" method="GET" role="search" action="/f1-webapp/views/public/search/search.php" name="search_bar">
                    <input class="form-control me-2" type="search" name="search" id="search_mobile" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-danger" type="submit">Search</button>
                </form>

            </div>
        </div>
    </div>
</nav>