#!/usr/bin/php
<?
/* This script is designed to be run on your Asterisk machine every minute    */
/* it creates a new outbound.conf file and compares it with the  */
/* existing one. */
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "SmoothOperator";

$connection = mysqli_connect($db_host,$db_user,$db_pass) or die("Error connecting to database: ".mysqli_error());
mysqli_select_db($connection, $db_name);

$result = mysqli_query($connection, "SELECT value from config where parameter = 'manager_outbound_trunk'") or die(mysqli_error($connection));
$outbound = "";
$row = mysqli_fetch_assoc($result);
$outbound = '[outbound_crm]'."\n";
$outbound .= 'exten => _X.,1,Dial('.$row['value'].",,t)\n";

$existing = file_get_contents("/etc/asterisk/outbound.conf");
if (!(strcmp($outbound, $existing) == 0)) {
    echo "Changes to outbound found\n";
    file_put_contents("/etc/asterisk/outbound.conf", $outbound);
    exec("asterisk -rx 'dialplan reload'");
}
?>