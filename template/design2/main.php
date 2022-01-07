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
        echo $data->lastGame->won ? "<span style='color:gold'>WON</span>" : "<span style='color:red'>LOST</span>";
        echo " <span style='color:white;'>(" . $data->lastGame->score . ")</span>";
    }
    ?>

</body>

</html>