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
$result = mysql_query("SELECT id, name FROM SineDialer.campaign where description = 'From SmoothOperator' and id in ($job_ids) ") or die(mysql_error());
?>
<table border="1">
<?
$header_printed = false;
while ($row = mysql_fetch_assoc($result)) {
    $result2 = mysql_query("SELECT * FROM SineDialer.queue where campaignID = ".$row['id']);
    if (mysql_num_rows($result2) > 0) {
        /* Has a queue entry associated */
        $highest = -100;
        while ($row2 = mysql_fetch_assoc($result2)) {
            /* A status of 1 means about to start, 2 means about to stop.      */
            /* Once processed it will add 100, so 101 means started, 102 means */
            /* stopped.  4 and 104 are for changing agent numbers.             */
            if ($row2['status'] >$highest && $row2['status'] != 104 && $row2['status'] != 4) {
                $highest = $row2['status'];
            }
            print_pre($row2);
        }
        $row['status'] = $highest;
    } else {
        /* Does not have a queue entry associated */
        echo "No Queue";
        $row['status'] = 0;
    }
    if (!$header_printed) {
        $header_printed = true;
        echo "<tr>";
        foreach ($row as $field=>$value) {
            echo "<th>".$field."</th>";
        }
        echo "</tr>";
    }
    echo "<tr>";
    foreach ($row as $field=>$value) {
        echo "<td>".$value."</td>";
    }
    echo "</tr>";
    print_pre($row);
}
?>
</table>
<?

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