<?php
if (!isset($lol)) {
    echo "Access forbidden.";
    die();
}
?>
<html>

<head>
    <meta http-equiv="refresh" content="5">
    <link href="/template/design1/main.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    if ($error->message !== "") {
        echo $error->message;
    } else {
    ?>
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
                    <?php echo $data->lost + $data->won; ?>
                </td>
                <td>
                    <?php echo round($data->won / ($data->lost + $data->won) * 100, 2); ?>%
                </td>
                <td>
                    <?php
                    if (count($data->leagueData) > 0) {
                        echo substr($data->leagueData[0]['tier'], 0, 1) . $data->leagueData[0]['rank'] . "<br/>" . $data->leagueData[0]['leaguePoints'] . "&nbsp;LP";
                    } else {
                        echo "N/A";
                    } ?>
                </td>
            </tr>
        </table>
        <div style=" text-align:left; margin-top: 10px;">

            <span style="color:white; margin-left: 10px;">Last:</span>
            <?php
            echo $data->lastGame->won ? "<span style='color:gold'>WON</span>" : "<span style='color:red'>LOST</span>";
            echo " <span style='color:white;'>(" . $data->lastGame->score . ")</span>";
            ?>
        </div>
        <?php
        for ($i = 7; $i >= 0; $i--) {
            $matchData = $lol->getSimpleMatchData($data->matches[$i]); //already cached so OK to query here
            echo '<div class="circle ' . ($matchData->won ? "green" : "red") . '"></div>';
        }
        ?>
    <?php
    }
    ?>

</body>

</html>