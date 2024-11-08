<?php function echo_news_cards($title_list, $img_list, $link_list, $max_render, $col_card): void { ?>
    <div class="row">
        <?php for ($i = 0; $i < min(count($title_list), $max_render); $i++) { ?>
            <div class="<?php echo htmlentities($col_card); ?> d-flex align-items-stretch py-3">
                <div class="card border border-danger border-3 p-2 d-flex flex-column justify-content-between">
                    <div class="card-img">
                        <img src="<?php echo htmlentities($img_list[$i]); ?>" class="card-img-top" alt="News thumbnail.">
                    </div>
                    <div class="card-body d-flex align-items-end">
                        <div class="w-100">
                            <h6 class="card-title text-danger"><?php echo htmlentities($title_list[$i]); ?></h6>
                            <hr>
                            <p class="card-text d-flex justify-content-between">
                                <label>
                                    <strong>NEWS</strong>
                                </label>
                                <a target="_blank" href="<?php echo htmlentities($link_list[$i]); ?>" class="card-link text-decoration-none d-flex flex-row justify-content-end">
                                    <span class="my_outline_animation d-flex flex-row gap-2 pb-1 hover-red">
                                        <span class="material-symbols-outlined">keyboard_double_arrow_right</span>
                                        Go
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
    <br>
    <div class="w-100 d-flex justify-content-center">
        <a target="_blank" href="https://www.formula1.com/en/latest/all" class="load-more my_outline_animation text-decoration-none hover-red d-flex align-items-end pb-2 gap-2 mb-3">
            <span class="material-symbols-outlined">keyboard_double_arrow_right</span>
            <span>Load more</span>
        </a>
    </div>
<?php } ?>