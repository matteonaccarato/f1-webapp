<?php function echo_drivers_cards($name_list, $team_list, $flag_list, $number_list, $img_list, $url_list, $col_card): void { ?>
    <div class="row">
        <?php for ($i = 0; $i < count($name_list); $i++) { ?>
            <div class="<?php echo $col_card ?> d-flex align-items-stretch py-3">
                <div id="div<?php echo $i ?>" class="w-100 card border border-danger border-3 p-2 d-flex flex-column justify-content-between">
                    <div id="num<?php echo $i ?>">
                        <div class="card-img">
                            <img src="<?php echo $img_list[$i] ?>" class="card-img-top" alt="<?php echo htmlentities($name_list[$i]) . " picture." ?>">
                        </div>
                        <div class="card-body d-flex align-items-end">
                            <div class="w-100">
                                <h6 class="card-title text-danger"><?php echo htmlentities($name_list[$i]); ?> </h6>
                                <p class="card-title text-secondary"><?php echo htmlentities($team_list[$i]); ?> </p>
                                <hr>
                                <p class="d-flex justify-content-between"><strong>Country: </strong><img style="position: relative; height: 20px; width: 30px" src="<?php echo $flag_list[$i] ?>" alt="Flag picture."></p>
                                <p class="d-flex justify-content-between"><strong>Number: </strong><img style="position: relative; left:4px; height: 20px; width: 40px" src="<?php echo $number_list[$i] ?>" alt="Number picture."></p>
                                <hr id="hr<?php echo $i; ?>">
                                <div class="d-flex justify-content-center">
                                    <button id="button<?php echo $i; ?>" type="button" class="btn card-link text-decoration-none d-flex justify-content-center">
                                            <span class="my_outline_animation d-flex flex-row gap-2 pb-1 hover-red">
                                                <span class="material-symbols-outlined">keyboard_double_arrow_right</span>
                                                More info
                                                <span class="material-symbols-outlined">sports_score</span>
                                            </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="info_driver<?php echo $i ?>">

                    </div>
                </div>
            </div>
        <script>f1_scrape_info_drivers('<?php echo $i ?>', '<?php echo $url_list[$i] ?>')</script>
        <?php } ?>
    </div>
<?php } ?>



