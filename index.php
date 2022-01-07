<?php
include "config.php";
include "lol.class.php";

if (!isset($_GET[$config->httpPassword])) {
    echo "Access forbidden.";
    die();
}
if (!isset($_GET['champion'])) {
    echo "Missing champion parameter.";
    die();
}

$design = (isset($_GET['design']) ? $_GET['design'] : "design1");


$key = $apiKey;
$lol = new lol($config->userName, $config->apiKey, $config->host, $config->regionHost);

include "template/" . $design . "/main.php";
