<?
if (isset($_GET['ajax'])) {
    /* Start or continue a logged in session */
    session_start();
    $config_values = $_SESSION['config_values'];
    
    /* Find out the current location */
    $current_directory = dirname(__FILE__);
    if (isset($override_directory)) {
        $current_directory = $override_directory;
    }
    
    /* Include VentureVoIP Functions */
    require "functions/functions.php";
    require "config/db_config.php";
    ?>
    <table class="sample" style="width:100%">
    <tr>
    <th>Member</th>
    <th>Location</th>
    <th>Calls Taken</th>
    <th>Status</th>
    
    </tr>
    <?
    /*
     [member] => Jacob
     [queue] => so_crm_2
     [location] => Agent/0005
     [membership] => realtime
     [calls_taken] => 0
     [status] => Offline
     [paused] => 0
     [penalty] => 0
     )
     
     Device States:
     
     0: AST_DEVICE_UNKNOWN      Device is valid but channel didn't know state
     1: AST_DEVICE_NOT_INUSE 	Device is not used
     2: AST_DEVICE_INUSE        Device is in use
     3: AST_DEVICE_BUSY         Device is busy
     4: AST_DEVICE_INVALID      Device is invalid
     5: AST_DEVICE_UNAVAILABLE 	Device is unavailable
     6: AST_DEVICE_RINGING      Device is ringing
     7: AST_DEVICE_RINGINUSE 	Device is ringing *and* in use
     8: AST_DEVICE_ONHOLD       Device is on hold
     
     */
    $result = mysqli_query($connection, "SELECT * FROM queue_member_status group by location");
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            switch ($row['status']) {
                case 1:
                    $status = '<img src="images/clock.png">&nbsp;'."Waiting for call";
                    break;
                case 2:
                case 3:
                case 6:
                case 7:
                    $status = '<img src="images/phone.png">&nbsp;'."On a call";
                    break;
                case 4:
                case 5:
                    $status = '<img src="images/cross.png">&nbsp;'."Offline";
                    break;
                default:
                    $status = "Unknown (".$row['status'].")";
                    break;
            }
            echo "<tr>";
            echo "<td>".$row['member']."</td>";
            echo "<td>".$row['location']."</td>";
            echo "<td>".$row['calls_taken']."</td>";
            echo "<td>".$status."</td>";
            //print_pre($row);
            echo "</tr>";
        }
    }
    ?>
    </table>
    <?
    //echo "<br />".@date("H:i:s");
    exit(0);
}
require "header.php";
?>
<br />
<div id="status" class="thin_700px_box">
</div>
<script>
jQuery("#status").load("realtime.php?ajax=1");
setInterval("reload()", 3000);
function reload() {
    jQuery("#status").load("realtime.php?ajax=1");
}
</script>
<?
require "footer.php";
?>