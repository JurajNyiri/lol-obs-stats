<?php
include "../../config.php";
include "../../data.php";
$returnData = new stdClass();

$returnData->data = "";
if (isset($error->message) && $error->message !== "") {
    $returnData->data = $error->message;
    echo json_encode($returnData);
    die();
}

if (!isset($_GET[$config->httpPassword])) {
    $returnData->data = "Access forbidden.";
    echo json_encode($returnData);
    die();
}

$returnData->data .= '
<div class="row">
    <div class="col-12 bigText headerTitle" style="font-size:30px;">
        Season 12
    </div>
</div>
';
$returnData->data .= '
    <div class="row w-100 bigText headerTitle px-0 mx-0">
        <div class="col-4 px-0 mx-0" style="padding-left:10px !important;">
            Games
        </div>
        <div class="col-4 px-0 mx-0" style="text-align:center;">
            WR
        </div>
        <div class="col-4 px-0 mx-0" style="padding-right:10px !important;">
            Rank
        </div>
    </div>';
$returnData->data .= '
    <div class="row w-100 d-flex align-items-center justify-content-center bigText headerContent px-0 mx-0">
        <div class="col-4 px-0 mx-0" style="padding-left:10px !important;">';
$returnData->data .= $data->lost + $data->won;
$returnData->data .= '</div>
        <div class="col-4 px-0 mx-0" style="text-align:center;">';

$returnData->data .= (($data->lost + $data->won === 0) ? "N/A" : round($data->won / ($data->lost + $data->won) * 100, 2) . "%");
$returnData->data .= '
</div>
<div class="col-4 px-0 mx-0" style="padding-right:10px !important;">';

if (count($data->leagueData) > 0) {
    $returnData->data .= substr($data->leagueData[0]['tier'], 0, 1) . $data->leagueData[0]['rank'] . "<br />" . $data->leagueData[0]['leaguePoints'] . "&nbsp;LP";
} else {
    $returnData->data .= "N/A";
}
$returnData->data .= '</div>
</div>';

$returnData->data .= '<div style="position:absolute; bottom:10px">';
$returnData->data .= '
<div class="row">
    <div class="col-4 bigText">
        <span style="color:white; margin-left: 10px;">Last:</span>';
$returnData->data .= '</div>';
$returnData->data .= '
    <div class="col-8 bigText" style="text-align:right;">';
$returnData->data .= $data->lastGame->won ? "<span style='color:gold'>WON</span>" : "<span style='color:red'>LOST</span>";
$returnData->data .= " <span style='color:white;'>(" . $data->lastGame->score . ")</span>";

$returnData->data .= '
    </div>
</div>';
$lastGames = array_reverse(array_slice($data->games, 0, 8));
foreach ($lastGames as $game) {
    $returnData->data .= '<div class="circle ' . ($game->won ? " green" : "red") . '"></div>';
}
$returnData->data .= '</div>';
echo json_encode($returnData);
