<?php
/** https://getcomposer.org/doc/00-intro.md */
/** https://copyprogramming.com/howto/php-php-get-image-by-xpath?utm_content=cmp-true */

if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");

const BASE_URL = "https://www.formula1.com/en/latest/all";
const MAX_NEWS_INDEX = 4;
const MAX_NEWS_NEWS = 6;

function f1_scrape_news($base_url): array {

    // Init arrays of interest
    $title_list = [];
    $img_list = [];
    $link_list = [];

    $page = file_get_contents($base_url);
    $html = new DOMDocument();
    @$html->loadHtml($page);
    $xpath = new DOMXPath($html);

    $node_list = $xpath->query('//img[@class="absolute left-0 top-0 right-0 bottom-0 w-full group-hover:tablet:scale-110 group-focus:tablet:scale-110 transition-transform duration-200"]');
    // Increasing by 2 each time because otherwise there would have been duplicates
    // In this situation I obtain 7 news elements
    for ($i=0; $i<MAX_NEWS_NEWS; $i += 1) {
        if ($node_list->item($i) == null)
            break;
        $link = $node_list->item($i)->getAttribute("src");

        # Normalize URL
        $pattern = "/(https:\/\/media\.formula1\.com\/image\/upload\/)(.*?)\/fom-website/";
        $replacement = "f_auto,c_limit,w_1440,q_auto/t_16by9Centre";
        $link = preg_replace($pattern, "$1{$replacement}/fom-website", $link);

        // Append in array
        $img_list[] = $link;
    }

    $node_list = $xpath->query('//figcaption[@class="px-5 tablet:pt-2 tablet:pb-[3rem] relative tablet:pt-2 tablet:pb-[3rem] transition-transform relative text-left col-span-3 tablet:col-span-5 group-hover:tablet:bg-carbonBlack group-focus:bg-carbonBlack    duration-300 group-hover:tablet:-translate-y-3 group-focus:tablet:-translate-y-3       "]');
    foreach ($node_list as $n) {
        $title = $n->nodeValue;

        /*
        Replace blank spaces in the read string, the limit of the replacements is just 2
            - Limit 2 imposed to obtain this situation (not affect the title)
                "News {title}" => ";News;{title}"
        */
        $title = preg_replace("/\s+/", ";", $title, 2);
        $title = explode(";", $title);
        /*
        Case analysis: "Feature;F1 Unlocked {text}"
            - index 2 to identify "F1 Unlocked {text}"
            - if there is "F1 Unlocked" then I sign it in a cell called title[3], otherwise it will be null
        */
        if (str_contains($title[2], "F1 Unlocked")) {
            $title[2] = preg_replace("/F1 Unlocked/", "", $title[2]);
            $title[3] = "F1 Unlocked";
        } else {
            $title[3] = null;
        }
        // example: ["News", "F1 Unlocked", "{title}"]
        // or: ["Feature", null, "{title}"]
        $title = [$title[1], $title[2], $title[3]];
        $title_list[] = $title;
    }

    $node_list = $xpath->query('//div[@class=" group w-full list-none focus-within:outline-blue-700 focus-within:bg-carbonBlack focus-within:outline focus-within:outline-2 leading-none tablet:rounded-b-2xl tablet:bg-white border-b-2 tablet:border-b-0 tablet:!border-0 tablet:py-0  bg-white hover:bg-carbonBlack focus:desktop:bg-carbonBlack        "]//a');
    foreach ($node_list as $n) {
        $link = $base_url . $n->getAttribute("href");
        $link_list[] = $link;
    }

    return [$title_list, $img_list, $link_list];
}