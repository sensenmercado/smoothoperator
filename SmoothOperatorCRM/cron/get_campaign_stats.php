#!/usr/bin/php
<?
/* This script is designed to be run from cron once every ten minutes to      */
/* update the status of campaign number allocations.                          */

require "../config/db_config.php";
require "../functions/sanitize.php";
$result = mysqli_query($connection, "SELECT parameter, value FROM SmoothOperator.config");
while ($row = mysqli_fetch_assoc($result)) {
    $config_values[$row['parameter']] = $row['value'];
}
$link = mysql_connect($config_values['smoothtorque_db_host'], $config_values['smoothtorque_db_user'], $config_values['smoothtorque_db_pass']) or die(mysql_error());
$result = mysql_query("SELECT campaignid, count(*), status FROM SineDialer.number group by campaignid, status") or die(mysql_error());
$date = @date("Y-m-d");
$hour = @date("H:i");
if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $details[$row['campaignid']][$row['status']] = $row['count(*)'];
    }
}
foreach ($details as $id=>$entries) {
    $sql1 = "REPLACE INTO SmoothOperator.campaign_stats (report_date, report_time, campaign_id, ";
    $sql2 = ") VALUES (".sanitize($date).",".sanitize($hour).",$id, ";
    foreach ($entries as $field=>$value) {
//        echo "$id - $field - $value\n";
        $sql1 .= sanitize($field, false).",";
        $sql2 .= sanitize($value, true).",";
    }
    $sql = substr($sql1,0,-1).substr($sql2,0,-1).")";
    //echo $sql."\n";
    mysqli_query($connection, $sql);
}
