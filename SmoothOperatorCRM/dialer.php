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

if (isset($_GET['start_campaign'])) {
    ?>
    <div id="starting" style="display: none" title="Campaign Starting">
    <center>
    <br />Please wait...starting your campaign.
    <br />
    <br />
    <div id="progress"></div>
    <br />
    <span id="start_status"></span>
    </center>
    </div>
    <script>
    jQuery("#starting").dialog({modal: true});
    </script>
    <?
    /* We now have a campaign id we'd like to use and a list id of phone numbers 
     we'd like to call.  Take the following actions:
     
     1. Connect to SmoothOperator
     2. Grab the numbers we'd like to call from the list
     3. Add a customer interraction for each number to say they are being sent 
     to SmoothTorque for dialling
     4. Connect to SmoothTorque
     5. Delete any existing numbers from that campaign
     6. Insert the new numbers
     7. Start the campaign     
     */
    
    $result = mysqli_query($connection, "SELECT distinct cleaned_number, id FROM customers WHERE list_id = ".sanitize($_GET['list_id']));
    
    $link = mysql_connect($config_values['smoothtorque_db_host'], $config_values['smoothtorque_db_user'], $config_values['smoothtorque_db_pass']) or die(mysql_error());
    $result_x = mysql_query("DELETE FROM SineDialer.number WHERE campaignid = ".sanitize($_GET['start_campaign']));
    
    $total = mysqli_num_rows($result);
    $i = 0;
    ?>
    <script>
    jQuery("#progress").progressbar({value: 0});
    </script>
    <?
    $number_sql_start = "REPLACE INTO SineDialer.number (campaignid, phonenumber, status, random_sort) VALUES ";
    $end_sql = "";
    $count = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $count++;
        $end_sql .= "(".sanitize($_GET['start_campaign']).",".sanitize($row['cleaned_number']).",'new',".sanitize(rand(0,99999999))."),";
        if ($count > 100) {
            $sql = $number_sql_start.substr($end_sql,0,strlen($end_sql)-1);
            $result_x = mysql_query($sql) or die(mysql_error());
            $end_sql = "";
            $count = 0;
        }
        
        $result2_x = mysqli_query($connection, "INSERT INTO SmoothOperator.interractions (contact_date_time, notes, customer_id) VALUES (NOW(), 'Sent for dialing', ".$row['id'].")");
        $i++;
        $perc = round($i/$total*100);
        //$perc = 30;
        if ($i % 30) {
            ?>
            <script>
            jQuery("#start_status").text("Moving <?=$row['cleaned_number']?> to dialer");
            
            jQuery( "#progress" ).progressbar( "option", "value", <?=$perc?> );
            </script>
            <?
            flush();
        }
    }
    if (strlen($end_sql) > 0) {
        $sql = $number_sql_start.substr($end_sql,0,strlen($end_sql)-1);
        $result_x = mysql_query($sql) or die(mysql_error());
    }
    $queue_name = "so_crm_".sanitize($_GET['start_campaign']-100000, false);
    $sql = "SELECT context, groupid, clid FROM SineDialer.campaign WHERE id = ".sanitize($_GET['start_campaign'], false);
    //echo $sql;
    $result_campaign = mysql_query($sql) or die(mysql_error());
    $row_campaign = mysql_fetch_assoc($result_campaign);
    $clid = $row_campaign['clid'];
    $context = $row_campaign['context'];
    
    
    
    // FOR TESTING: USE LOAD SIMULATION CONTEXT - CHANGE BACK AFTER
    $context = 0;
    
    
    
    //print_pre($row_campaign);
    $result_customer = mysql_query("SELECT * FROM SineDialer.customer WHERE campaigngroupid = ".$row_campaign['groupid']) or die(mysql_error());
    $row_customer = mysql_fetch_assoc($result_customer);
    //print_pre($row_customer);
    $account = "stl-".$row_customer['username'];
    $row_trunk = array();
    if ($row_customer['trunkid'] == -1) {
        // Using default trunk
        $result_trunk = mysql_query("SELECT * FROM SineDialer.trunk WHERE current = 1");
        $row_trunk = mysql_fetch_assoc($result_trunk);
    } else {
        $trunkid = $row_customer['trunkid'];
        $result_trunk = mysql_query("SELECT * FROM SineDialer.trunk WHERE id = ".$trunkid) or die(mysql_error());
        $row_trunk = mysql_fetch_assoc($result_trunk);
    }
    
    $trunk = $row_trunk['dialstring'];
    $maxchans = $row_trunk['maxchans'];
    $maxcps = $row_trunk['maxcps'];
    $trunk = $row_trunk['dialstring'];
    $account ="stl-matt";
    $trunkid = 506;
    $customerid = 1;
    echo $queue_name;
    $sql = "INSERT INTO SineDialer.queue (`queuename`, `status`, `campaignID`, `details`, `flags`, `transferclid`, `starttime`, `endtime`, `startdate`, `enddate`, `did`, `clid`, `context`, `maxcalls`, `maxchans`, `maxretries`, `retrytime`, `waittime`, `timespent`, `progress`, `expectedRate`, `mode`, `astqueuename`, `trunk`, `accountcode`, `trunkid`, `customerID`, `maxcps`, `drive_min`, `drive_max`)
    VALUES
    ('crm-autostart-".sanitize($_GET['start_campaign']-100000, false)."', 1, ".sanitize($_GET['start_campaign']-100000, false).", 'No details', 0, 'nocallerid', '00:00:00', '23:59:00', '2005-01-01', '2020-01-01', 'nodid', '$clid',$context, 30, $maxchans, 0, 0, 30, '0', '-1', 100, '1', '$queue_name', '$trunk', '$account', $trunkid, $customerid, $maxcps, '43.0', '61.0')";
    echo $sql;
    //$result = mysql_query($sql);
    ?>
    <script>
    jQuery("#starting").dialog("close");
    </script>
    <?
    require "footer.php";
    exit(0);
}

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
<script>
var $the_dialog;
jQuery(document).ready(function() {
                       $the_dialog = jQuery('#start_campaign_dialog').dialog({
                                                                             autoOpen: false,
                                                                             modal: true,
                                                                             buttons: {
                                                                             Ok: function() {
                                                                             var list_id = jQuery("#lists_to_run").val();
                                                                             var campaign_id = jQuery("#campaign_id").val();
                                                                             //alert(list_id+" "+campaign_id);
                                                                             window.location = "dialer.php?start_campaign="+campaign_id+"&list_id="+list_id;
                                                                             }
                                                                             }
                                                                             });
                       });
function show_start(campaign_id, title) {
    jQuery('#start_campaign_dialog').dialog("option","title","Start "+title);
    jQuery("#campaign_id").val(campaign_id);
    $the_dialog.dialog('open');
}
</script>
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
                $row['progress'] = $row2['progress'];
                $row['busy'] = $row2['flags'];
                $row['total'] = $row2['maxcalls'];
            }
            //print_pre($row2);
        }
        $row['status'] = $highest;
        if ($row['total'] > 0) {
            $row['percentage_busy'] = round($row['busy']/$row['total']*100,2);
        } else {
            $row['percentage_busy'] = 0.00;
        }
    } else {
        /* Does not have a queue entry associated */
        //echo "No Queue";
        $row['progress'] = 0;
        $row['busy'] = 0;
        $row['total'] = 0;
        $row['status'] = 0;
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
    //$row['percentage_busy']=30;
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
                    echo '<td '.$style.'><center><a href="#" onclick="show_start('.$row['id'].',\''.$row['name'].'\');return false;">Not Running&nbsp;<img src="images/control_play_blue.png" alt="Stop Campaign" border="0" valign="middle"></a></center></td>';
                    break;
            }
        } else if ($field == "id") {
            
        } else if ($field == "percentage_busy") {
            echo "<td $style><center>";
            ?>
            <div id="perc_busy_<?=$row['id']?>" style="height: 10px; width: 150px"></div>
            <script>
            jQuery("#perc_busy_<?=$row['id']?>").progressbar({value: <?=$value?>});
            </script>
            <?
            echo "</center></td>";
            /*} else if ($field == "progress") {
             echo "<td $style><center>";
             ?>
             <div id="progress_<?=$row['id']?>" style="height: 10px; width: 150px"></div>
             <script>
             jQuery("#progress_<?=$row['id']?>").progressbar({value: <?=$value?>});
             </script>
             <?
             echo "</center></td>";*/
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
<div id="start_campaign_dialog" style="display: none">
<center>
<br />
Please select a list to run:<br />
<br />
<?

$result = mysqli_query($connection, "SELECT count(*) as count, list_id, lists.name FROM customers, lists where customers.list_id = lists.id group by customers.list_id") or die(mysqli_error($connection));
if (mysqli_num_rows($result) == 0) {
    /* No lists */
} else {
    echo '<select name="list_to_run" id="lists_to_run">';
    while ($row = mysqli_fetch_assoc($result)) {
        //        print_pre($row);
        echo '<option value="'.$row['list_id'].'">'.$row['name'].' ('.$row['count'].' numbers)</option>';
    }
    echo '</select>';
    ?>
    <input type="hidden" name="campaign_id" id="campaign_id" value="x">
    <?
}
echo "</center></div>";
require "footer.php";
?>