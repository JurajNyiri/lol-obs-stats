<?php
include "config.php";
include "lol.class.php";

$data = new stdClass();
$error = new stdClass();

$error->progress = 0;
$error->message = "";

if (!isset($_GET[$config->httpPassword])) {
    $error->message = "Access forbidden.";
}

$design = (isset($_GET['design']) ? $_GET['design'] : "design1");

$lol = new lol($config->userName, $config->apiKey, $config->host, $config->regionHost);


$data->matches = $lol->getAllMatches();
if ($data->matches === false) {
    $error->message = "API Limit hit. Keep this page opened and it will download more data.<br/>Download progress: waiting...";
}

$data->leagueData = $lol->getLeagueData();
if ($data->leagueData === false) {
    $error->message = "API Limit hit. Keep this page opened and it will download more data.<br/>Download progress: waiting...";
}

if ($data->leagueData !== false && $data->matches !== false) {
    $data->lastGame = false;
    $data->won = 0;
    $data->lost = 0;
    $downloadedCount = 0;
    $data->games = array();
    foreach ($data->matches as $i => $match) {
        $matchData = $lol->getSimpleMatchData($match);
        if ($matchData->gameMode === "CLASSIC") {
            if ($matchData === false) {
                $error->progress = round($downloadedCount / count($data->matches) * 100, 1);
                $error->message = "API Limit hit. Keep this page opened and it will download more data.<br/>Download progress: " . $error->progress . "%";
                break;
            } else {
                $downloadedCount++;
                if ($data->lastGame === false) {
                    $data->lastGame = $matchData;
                }
                if (!isset($_GET['champion']) || $matchData->champion === $_GET['champion']) {
                    if ($matchData->won) {
                        $data->won++;
                    } else {
                        $data->lost++;
                    }
                }
            }
            array_push($data->games, $matchData);
        }
    }
}

include "template/" . $design . "/main.php";
