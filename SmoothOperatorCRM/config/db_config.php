<?php
    $db_host = "data.venturevoip.com";
    $db_user = "sdialer";
    $db_pass = "sdp4ss";
    $db_name = "stats";

    $connection = @mysql_connect($db_host,$db_user,$db_pass) or die("Error connecting to database");
    mysql_select_db("SmoothOperator", $connection);

?>
