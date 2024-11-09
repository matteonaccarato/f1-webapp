<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");

const BASE_URL = "https://www.formula1.com/en/drivers.html";
const BASE_F1 = "https://www.formula1.com";

function f1_scrape_drivers($base_url): array {

    // Init arrays of interest
    $team_list = [];
    $img_list = [];
    $number_list = [];
    $name_list = [];
    $flag_list = [];
    $url_list = [];

    $page = file_get_contents($base_url);
    $html = new DOMDocument();
    @$html->loadHtml($page);
    $xpath = new DOMXPath($html);

    // TEAMS
    $node_list = $xpath->query('//p[@class="f1-heading tracking-normal text-fs-12px leading-tight normal-case font-normal non-italic f1-heading__body font-formulaOne text-greyDark"]');
    foreach ($node_list as $n) {
        $team = $n->nodeValue;
        $team_list[] = $team;
    }

    // NAMES
    $node_list = $xpath->query('//div[contains(@class, "f1-driver-name")]');
    foreach ($node_list as $n) {
        $name = "";
        foreach ($n->childNodes as $n_inner) {
            $name .=  $n_inner->nodeValue . " ";
        }

        $name_list[] = $name;
    }

    // FLAGS
    $node_list = $xpath->query('//img[@class="f1-c-image h-[2em] ml-auto mr-0 border border-greyDark rounded-xxs"]');
    foreach ($node_list as $node) {
        $flag_list[] = $node->getAttribute("src");
    }

    // NUMBERS
    $node_list = $xpath->query('//img[@class="f1-c-image f1-utils-square-block text-[6rem]"]');
    foreach ($node_list as $node) {
        $number_list[] = $node->getAttribute("src");
    }

    // IMGs
    $node_list = $xpath->query('//img[@class="f1-c-image ml-0 mr-0 pr-s max-w-3/4"]');
    foreach ($node_list as $node) {
        $img_list[] = $node->getAttribute("src");
    }

    // Get EXTRA INFO
    $node_list = $xpath->query('//a[@class="group focus-visible:outline-0"]');
    foreach ($node_list as $node) {
        $url_list[] = BASE_F1 . $node->getAttribute("href");
    }

    return [$name_list, $team_list, $flag_list, $number_list, $img_list, $url_list];
}