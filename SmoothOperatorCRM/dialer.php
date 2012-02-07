<?
/*
 SmoothTorque Dialer Integration
 ===============================
 
 You need two things:
 
 1. A list you would like to run
 2. A job you would like to run
 
 From this main page you can view running campaigns, add/remove numbers and
 start new campaigns running
 
 */
require "header.php";

$result = mysqli_query($connection, "SELECT * FROM jobs");
$job_ids = "";
if (mysqli_num_rows($result) == 0) {
    /* No jobs */
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        $job_ids.= (100000+$row['id']).",";
    }
}
/* cut off the last comma */
$job_ids = substr($job_ids,0,-1);

$link = mysql_connect($config_values['smoothtorque_db_host'], $config_values['smoothtorque_db_user'], $config_values['smoothtorque_db_pass']) or die(mysql_error());
$result = mysql_query("SELECT * FROM SineDialer.campaign where description = 'From SmoothOperator' and id in ($job_ids) ") or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $result2 = mysql_query("SELECT * FROM SineDialer.queue where campaignID = ".$row['id']);
    if (mysql_num_rows($result2) > 0) {
        /* Has a queue entry associated */
        $highest = -100;
        while ($row2 = mysql_fetch_assoc($result2)) {
            if ($row['status'] >$highest && $row['status'] != 104 && $row['status'] != 4) {
                $highest = $row['status'];
            }
        }
        $row['status'] = $highest;
    } else {
        /* Does not have a queue entry associated */
        echo "No Queue";
        $row['status'] = 0;
    }
    print_pre($row);
}

$result = mysqli_query($connection, "SELECT count(*) as count, list_id, lists.name FROM customers, lists where customers.list_id = lists.id group by customers.list_id") or die(mysqli_error($connection));
if (mysqli_num_rows($result) == 0) {
    /* No lists */
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        print_pre($row);
    }
}
require "footer.php";
?>