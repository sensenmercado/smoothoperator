<?
session_start();

/* Connect to the database */
require "config/db_config.php";

/* Include sanitize functions */
require "functions/sanitize.php";

/* Get the current channel name */
$result = mysqli_query($connection, "SELECT data1 FROM queue_log WHERE event = 'AGENTLOGIN' AND agent = 'Agent/".$_SESSION['agent_num']."' order by id desc limit 1") or die(mysqli_error($connection));

$row = mysqli_fetch_assoc($result);
if (mysqli_num_rows($result) < 1) {
    //echo "eek";
    return;
}
$chan = $row['data1'];
//sleep(5);
$repeat = false;
while ($repeat) {
    $result = mysqli_query($connection, "SELECT bridged_channel FROM channels WHERE channel = ".sanitize($chan));
    if (mysqli_num_rows($result) == 0) {
        $display_pop = false;
    } else {
        $row = mysqli_fetch_assoc($result);
        if (strlen(trim($row['bridged_channel'])) > 1) {
            $display_pop = true;
            $bridged = $row['bridged_channel'];
        } else {
            $display_pop = false;
        }
    }
    
    $sql = "SELECT * FROM phone_calls WHERE extension = ".sanitize($_GET['extension']);
    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($display_pop) {
            echo str_replace("+1","",$row['callerid']);
            //echo $bridged;
            mysqli_query($connection, "DELETE FROM phone_calls where id = ".$row['id']);
        } else {
            $repeat = true;
            usleep(200000);
        }
    }
}
?>