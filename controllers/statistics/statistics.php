<?php
const HEADING_TEXTS_COUNT = 6;
function f1_scrape_stat($base_url): array {

    // Init storing arrays
    $races = [];
    $dates = [];
    $winners = [];
    $teams = [];
    $laps = [];
    $times = [];
    $links = [];

    $page = file_get_contents($base_url);
    $html = new DOMDocument();
    @$html->loadHtml($page);
    $xpath = new DOMXPath($html);

    # [GP-NAME, DATE, WINNER, TEAM, LAPS, TIME]
    $selector = 0;
    $node_list = $xpath->query('//p[contains(@class, "f1-text")]');
    $i = HEADING_TEXTS_COUNT; # Skip heading texts
    while ($i < count($node_list)) {
        $node = $node_list->item($i)->nodeValue;
        switch ($selector) {
            case 0: $races[] = $node; break;
            case 1: $dates[] = $node; break;
            case 2: $winners[] = substr_replace($node, ' ', -3, 0); break; // Fernando AlonsoALO -> Fernando Alonso ALO
            case 3: $teams[] = $node; break;
            case 4: $laps[] = $node; break;
            case 5: $times[] = $node; break;
            default: break;
        }
        $selector = ($selector + 1) % 6;
        $i += 1;
    }

    // Get Links
    $node_list = $xpath->query('//a[@class="underline underline-offset-normal decoration-1 decoration-greyLight hover:decoration-brand-primary"]');
    foreach ($node_list as $n) {
        $link = $n->getAttribute("href");
        $base_url = str_replace(".html", "", $base_url);
        $base_url = str_replace("races", "", $base_url);

        $links[] = $base_url . $link;
    }

    return [$races, $dates, $winners, $teams, $laps, $times, $links];
}