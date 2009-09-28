<?php
    //phpinfo();
    $db_host = "data.venturevoip.com";
    $db_user = "so123";
    $db_pass = "soy487h";
    $db_name = "SmoothOperator";

    $connection = @mysql_connect($db_host,$db_user,$db_pass, false) or die("Error connecting to database: ".mysql_error());
    mysql_select_db($db_name, $connection);

?>
