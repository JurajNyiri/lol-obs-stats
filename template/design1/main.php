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
    <link href="/template/design1/main.css" rel="stylesheet" type="text/css">
</head>

<body>
    <table>
        <tr>
            <td style="color:white">
                Games
            </td>
            <td style="color:white">
                WR
            </td>
            <td style="color:white">
                Rank
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $lost + $won; ?>
            </td>
            <td>
                <?php echo round($won / ($lost + $won) * 100, 2); ?>%
            </td>
            <td>
                <?php
                if (count($leagueData) > 0) {
                    echo substr($leagueData[0]['tier'], 0, 1) . $leagueData[0]['rank'] . "<br/>" . $leagueData[0]['leaguePoints'] . "&nbsp;LP";
                } else {
                    echo "N/A";
                } ?>
            </td>
        </tr>
    </table>
    <div style=" text-align:left; margin-top: 10px;">

        <span style="color:white; margin-left: 10px;">Last:</span>
        <?php
        echo $lastGame->won ? "<span style='color:gold'>WON</span>" : "<span style='color:red'>LOST</span>";
        echo " <span style='color:white;'>(" . $lastGame->score . ")</span>";
        ?>
    </div>
    <?php
    for ($i = 7; $i >= 0; $i--) {
        $matchData = $lol->getSimpleMatchData($matches[$i]);
        echo '<div class="circle ' . ($matchData->won ? "green" : "red") . '"></div>';
    }
    ?>

</body>

</html>