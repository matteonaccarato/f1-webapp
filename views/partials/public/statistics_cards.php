<?php function echo_stat_cards($races, $dates, $winners, $teams, $laps, $times, $links, $col_card): void { ?>
    <div class="row">
        <?php for ($i = 0; $i < count($races); $i++) { ?>
            <a class="<?php echo $col_card ?> d-flex align-items-stretch py-3" style="text-decoration: none;" href="<?php echo htmlentities($links[$i]); ?>" target="_blank">
                <div class="w-100 card border border-danger border-3 p-2 d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="d-flex justify-content-center text-align-center"><strong><?php echo htmlentities($races[$i]) ?></strong></h4>
                        <hr>
                        <div class="card-body d-flex align-items-end">
                            <div class="w-100">
                                <ul>
                                    <li>
                                        <h6 class="card-title text-danger"><strong>Date: </strong><?php echo htmlentities($dates[$i]); ?> </h6>
                                    </li>
                                    <li>
                                        <h6 class="card-title text-danger"><strong>Winner: </strong> <?php echo htmlentities($winners[$i]); ?> </h6>
                                    </li>
                                    <li>
                                        <h6 class="card-title text-danger"><strong>Team: </strong><?php echo htmlentities($teams[$i]); ?> </h6>
                                    </li>
                                    <li>
                                        <h6 class="card-title text-danger"><strong>Laps: </strong><?php echo htmlentities($laps[$i]); ?> </h6>
                                    </li>
                                    <li>
                                        <h6 class="card-title text-danger"><strong>Time: </strong><?php echo htmlentities($times[$i]); ?> </h6>
                                    </li>
                                </ul>
                                <hr>
                            </div>
                        </div>
                    </div>
                    <div id="info_driver<?php echo $i ?>">

                    </div>
                </div>
            </a>
        <?php } ?>
    </div>
<?php } ?>





