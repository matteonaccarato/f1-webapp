<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-5 g-4">

    <?php if ($num_products > 0) { ?>

        <?php $i = 0; ?>
        <?php foreach ($products as $product) { ?>

            <div class="d-none col d-flex align-items-stretch product" id="product-<?php echo htmlentities($i); ?>">
                <a href="/f1-webapp/views/public/store/product.php?id=<?php echo htmlentities($product["Products.id"]); ?>" class="w-100 text-decoration-none">
                    <div class="card bordered border-danger border-3 p-2 h-100">
                        <div class="card-img">
                            <?php if (explode("\t", $product["Products.img_url"])[0] != "") { ?>
                                <img src="<?php echo htmlentities(explode("\t", $product["Products.img_url"])[0]); ?>" class="card-img-top" alt="<?php $alt = explode("\t", $product["Products.alt"])[0]; echo htmlentities(($alt !== "")?$alt:$product["products.title"]); ?>">
                            <?php } ?>
                        </div>
                        <div class="card-body d-flex align-items-end p-1">
                            <div class="w-100">
                                <h5 class="card-title text-danger"><?php echo htmlentities($product["Products.title"]); ?></h5>
                                <hr>
                                <p class="card-text"><?php echo htmlentities((strlen($product["Products.description"]) < 50)? $product["Products.description"] : (substr($product["Products.description"], 0, 70) . " [...]")); ?></p>
                                <div class="card-text text-decoration-none d-flex justify-content-between align-items-end pt-3">
                                    <h5 style="border-top: 2px solid red; border-right: 2px solid red; padding-right: 5px;" class="h-100 d-flex align-items-center">
                                        <?php [$int, $dec] = str2int_dec($product["Products.price"]); ?>
                                        <strong>€ <?php echo htmlentities($int . "." . $dec); ?></strong>
                                    </h5>
                                    <span <?php echo get_data_id($product); ?> id="span-add-it-<?php echo $product["Products.id"]; ?>" class="btn-modal d-flex flex-row gap-2 pb-1 hover-red">
                                        <span <?php echo get_data_id($product); ?> data-bs-toggle="modal" data-bs-target="#modal-<?php echo $product["Products.id"]; ?>" class="btn-add-cart btn-modal btn-reverse-color btn btn-danger d-flex justify-content-center align-items-center gap-2">
                                            <span <?php echo get_data_id($product); ?> class="btn-modal material-symbols-outlined">shopping_bag</span>
                                            <span <?php echo get_data_id($product); ?> class="btn-modal">Add it!</span>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Modal (to choose size) -->
            <div class="modal fade" id="modal-<?php echo $product["Products.id"]; ?>" tabindex="-1" aria-labelledby="modal-<?php echo $product["Products.id"]; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select size</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php $size = explode(";", $product["Products.size"]); ?>
                            <select id="s-size-<?php echo $product["Products.id"]; ?>" class="form-select rounded-pill" aria-label="Select size">
                                <option value="" class="option_invalid" selected>Select size</option>
                                <?php
                                foreach ($size as $s) {
                                    echo "<option value='$s' class='option_valid'>" . htmlentities(strtoupper($s)) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary my_close" id="close-modal-<?php echo $product["Products.id"]; ?>" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger btn-reverse-color my_confirm" id="confirm-modal-<?php echo $product["Products.id"]; ?>" data-bs-dismiss="modal">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $i++;
        }
        ?>

    <?php } else { ?>
        <div class="mx-auto alert alert-no-data border-light fade show d-flex align-items-center justify-content-center mt-4 col-12" role="alert">
            <span class="material-symbols-outlined">description</span>
            <span class="mx-2">
                <b>INFO</b>&nbsp;| No Data available!
            </span>
        </div>
    <?php } ?>
</div>