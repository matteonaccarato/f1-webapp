<?php
/** https://getcomposer.org/doc/00-intro.md */
/** https://copyprogramming.com/howto/php-php-get-image-by-xpath?utm_content=cmp-true */

if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");

const BASE_URL = "https://www.formula1.com/en/latest/all";
const BASE_F1 = "https://www.formula1.com";

const MAX_NEWS_INDEX = 4;
const MAX_NEWS_NEWS = 6;

function f1_scrape_news($base_url): array {

    $title_list = [];
    $img_list = [];
    $link_list = [];

    $page = file_get_contents($base_url);

    if ($page === false) {
        die("Could not retrieve page");
    }

    $html = new DOMDocument();
    @$html->loadHTML($page);
    $xpath = new DOMXPath($html);

    $node_list = $xpath->query(
        '//a[contains(@href, "/en/latest/article/")]'
    );

    // Extract article-card images in page order.
    preg_match_all(
        '/<span[^>]*class="[^"]*ArticleListCard-module_image__[^"]*"[^>]*>.*?(https:\/\/media\.formula1\.com\/image\/upload\/[^"\'\\\\\s]+).*?<\/span>/s',
        $page,
        $image_matches
    );

    $card_images = [];
    foreach ($image_matches[1] as $image_url) {
        $image_url = html_entity_decode($image_url);
        $image_url = rtrim($image_url, "\\ \t\n\r");
        $card_images[] = $image_url;
    }

    $image_index = 0;
    foreach ($node_list as $node) {
        // TITLE
        $title = trim(
            preg_replace(
                '/\s+/',
                ' ',
                $node->textContent
            )
        );

        // LINK
        $article_path = $node->getAttribute("href");
        $link = str_starts_with($article_path, "/")
            ? BASE_F1 . $article_path
            : $article_path;

        // IMAGE
        $image_url = $card_images[$image_index] ?? "";
        $image_index++;

        // Skip quiz articles
        if (stripos($title, 'QUIZ:') === 0) {
            continue;
        }

        // Normalize image URL
        if ($image_url !== "") {
            $pattern =
                '/(https:\/\/media\.formula1\.com\/image\/upload\/)(.*?)\/fom-website/';
            $replacement =
                'f_auto,c_limit,w_1440,q_auto/t_16by9Centre';
            $image_url = preg_replace(
                $pattern,
                '$1' . $replacement . '/fom-website',
                $image_url
            );
        }

        $title_list[] = $title;
        $img_list[] = $image_url;
        $link_list[] = $link;

        // Maximum number of articles
        if (count($title_list) >= MAX_NEWS_NEWS) {
            break;
        }
    }

    $title_list = array_slice(
        $title_list,
        0,
        MAX_NEWS_NEWS
    );

    $img_list = array_slice(
        $img_list,
        0,
        MAX_NEWS_NEWS
    );

    $link_list = array_slice(
        $link_list,
        0,
        MAX_NEWS_NEWS
    );

    return [
        $title_list,
        $img_list,
        $link_list
    ];
}