<?php

date_default_timezone_set("Europe/Rome");
$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__);
$_ENV["DOCUMENT_ROOT"] = $_SERVER["DOCUMENT_ROOT"];

if (!class_exists("DB", false)) {
    require_once dirname(__DIR__) . "/DB/DB.php";
}
