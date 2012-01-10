#!/usr/bin/php
<?
/* This script is designed to be run on your Asterisk machine every minute    */
/* it creates a new smoothoperator_agents.conf file and compares it with the  */
/* existing one. */
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "SmoothOperator";

$connection = mysqli_connect($db_host,$db_user,$db_pass) or die("Error connecting to database: ".mysqli_error());
mysqli_select_db($connection, $db_name);

$result = mysqli_query($connection, "SELECT * FROM agent_nums WHERE used = 1");
$agents = "";
while ($row = mysqli_fetch_assoc($result)) {
    //print_r($row);
    $agents.= 'agent => '.$row['agent_num'].','.$row['pin'].",SmoothOperator Agent\n";
}
$existing = file_get_contents("/etc/asterisk/smoothoperator_agents.conf");
if (!(strcmp($agents, $existing) == 0)) {
    echo "Changes to agents found\n";
    file_put_contents("/etc/asterisk/smoothoperator_agents.conf", $agents);
    exec("asterisk -rx 'module reload chan_agent.so'");
}
?>