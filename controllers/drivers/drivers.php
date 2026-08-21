<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");

const BASE_URL = "https://www.formula1.com/en/drivers";
const BASE_F1 = "https://www.formula1.com";

function f1_scrape_drivers($base_url): array {

    $team_list = [];
    $img_list = [];
    $number_list = [];
    $name_list = [];
    $flag_list = [];
    $url_list = [];

    $page = file_get_contents($base_url);

    if ($page === false) {
        die("Could not retrieve page");
    }

    $html = new DOMDocument();
    @$html->loadHTML($page);
    $xpath = new DOMXPath($html);

    /*
     * Each driver card is an <a> whose href starts with /en/drivers/
     */
    $driver_cards = $xpath->query(
        '//a[starts-with(@href, "/en/drivers/")]'
    );

    foreach ($driver_cards as $card) {

        /*
         * URL
         */
        $url_list[] = BASE_F1 . $card->getAttribute('href');


        /*
         * NAME
         *
         * The name is available in the driver-card context:
         * data-f1rd-a7s-context
         *
         * Example:
         * ..."driverName":"Kimi Antonelli","driverTeam":"Mercedes"...
         */
        $context = $card->getAttribute('data-f1rd-a7s-context');

        $data = json_decode($context, true);

        if (isset($data['driverName'])) {
            $name_list[] = $data['driverName'];
        } else {
            $name_list[] = '';
        }


        /*
         * TEAM
         */
        if (isset($data['driverTeam'])) {
            $team_list[] = $data['driverTeam'];
        } else {
            $team_list[] = '';
        }


        /*
         * DRIVER IMAGE
         */
        $img = $xpath->query(
            './/img[@role="presentation"]',
            $card
        )->item(0);

        if ($img !== null) {
            $img_list[] = $img->getAttribute('src');
        } else {
            $img_list[] = '';
        }


        /*
         * FLAG
         *
         * Find SVG/image associated with the driver card.
         */
        $flag = $xpath->query(
            './/img',
            $card
        );

        $flag_src = '';

        foreach ($flag as $img_node) {
            $src = $img_node->getAttribute('src');

            if ($src !== '') {
                $flag_src = $src;
                break;
            }
        }

        $flag_list[] = $flag_src;


        /*
         * NUMBER
         *
         * Find the number image in this driver card.
         */
        $number = $xpath->query(
            './/img[contains(@class, "h-em-24 w-em-96 bg-static-static-1")]',
            $card
        )->item(0);

        if ($number !== null) {
            $number_list[] = $number->getAttribute('src');
        } else {
            $number_list[] = '';
        }
    }
    return [
        $name_list,
        $team_list,
        $flag_list,
        $number_list,
        $img_list,
        $url_list
    ];
}