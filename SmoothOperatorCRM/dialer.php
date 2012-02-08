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
$rounded[] = "div.thin_700px_box";
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
<div class="thin_700px_box">
<table class="sample2" width="100%">
<tbody>
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
            /* stopped.  3 and 103 are for changing agent numbers.             */
            if ($row2['status'] >$highest && $row2['status'] != 103 && $row2['status'] != 3) {
                $highest = $row2['status'];
            }
            //print_pre($row2);
        }
        $row['status'] = $highest;
        $row['progress'] = $row2['progress'];
        $row['busy'] = $row2['flags'];
        $row['total'] = $row2['maxcalls'];
        if ($row['total'] > 0) {
            $row['percentage_busy'] = round($row['busy']/$row['total']*100,2);
        } else {
            $row['percentage_busy'] = 0.00;
        }
    } else {
        /* Does not have a queue entry associated */
        //echo "No Queue";
        $row['status'] = 0;
        $row['progress'] = 0;
        $row['busy'] = 0;
        $row['total'] = 0;
        $row['percentage_busy'] = 0.00;
    }
    
    $result_x = mysqli_query($connection, "SELECT name FROM jobs WHERE id = ".($row['id']-100000));
    $row_x =mysqli_fetch_assoc($result_x);
    $row['name'] = $row_x['name'];
    
    if (!$header_printed) {
        $header_printed = true;
        echo "<tr>";
        foreach ($row as $field=>$value) {
            if ($field == "progress") {
                echo '<th><center>Dialed</center></th>';
            } else if ($field == "id") {
            } else {
                echo '<th><center>'.ucfirst(str_replace("_"," ",$field))."</center></th>";
            }
            
        }
        echo "</tr>";
    }
    echo "<tr>";
    if ($row['status'] == 1 && $row['status'] == 101) {
        // Campaign is running
        $style=' style="background: #cfc"';
    } else {
        // Campaign is not running
        $style=' style="background: #fff"';
    }
    foreach ($row as $field=>$value) {
        if ($field == "status") {
            switch ($value) {
                case 1:
                case 101:
                    // Running
                    echo '<td '.$style.'>Running&nbsp;<a href="dialer.php?stop='.$row['id'].'"><img src="images/control_stop_blue.png" alt="Stop Campaign" border="0" valign="middle"></a></td>';
                    break;
                case 103:
                case 104:
                case 3:
                case 4:
                case -1:
                case 102:
                case 0:
                case 2:
                default:
                    // Not running
                    echo '<td '.$style.'><center><a href="dialer.php?start='.$row['id'].'">Not Running&nbsp;<img src="images/control_play_blue.png" alt="Stop Campaign" border="0" valign="middle"></a></center></td>';
                    break;
            }
        } else if ($field == "id") {
            
        } else {
            echo "<td $style><center>".$value."</center></td>";
        }
    }
    echo "</tr>";
    //print_pre($row);
}
?>
</tbody>
</table>
</div>
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