<?php
if (!isset($lol)) {
    echo "Access forbidden.";
    die();
}
$leagueData = $lol->getLeagueData();

$matches = $lol->getAllMatches();

$lastGame = false;
$won = 0;
$lost = 0;
foreach ($matches as $match) {
    $matchData = $lol->getSimpleMatchData($match);
    if ($lastGame === false) {
        $lastGame = $matchData;
    }
    if ($matchData->champion === $_GET['champion']) {
        if ($matchData->won) {
            $won++;
        } else {
            $lost++;
        }
    }
}

?>
<html>

<head>
    <meta http-equiv="refresh" content="5">
    <link href="/template/design2/main.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    echo $lastGame->won ? "<span style='color:gold'>WON</span>" : "<span style='color:red'>LOST</span>";
    echo " <span style='color:white;'>(" . $lastGame->score . ")</span>";
    ?>
</body>

</html>