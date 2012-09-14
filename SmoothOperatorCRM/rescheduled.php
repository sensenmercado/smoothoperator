<?
require "header.php";
if (isset($_GET['call_number'])) {
    ?>
    <div id="calling" style="display:none" title="Outgoing call"><center>
    <br />
    Calling <?=$_GET['call_number']?>...<br />
    <br />
    <img src="images/progress.gif">
    </div>
    <script>
    jQuery("#calling").dialog();
    </script>
    <?
    $response = agent_dial(sanitize($_GET['call_number'], false));
    $result = explode("\r\n",$response);
    foreach ($result as $line) {
        if (trim($line) == "Message: Originate successfully queued") {
            redirect("get_customer.php?from=orginate&phone_number=".$_GET['call_number']);
        }
    }
    require "footer.php";
    exit(0);
}
$result = mysqli_query($connection, "SELECT * FROM SmoothOperator.reschedule WHERE user = ".SANITIZE($_SESSION['user_id']));
echo '<div class="thin_700px_box">';
if (mysqli_num_rows($result) == 0) {
    echo "You have no rescheduled calls";
} else {
    ?>
    <table class="sample">
    <tr>
    <th>Date/Time</th>
    <th>Phone Number</th>
    <th>Done</th>
    </tr>
    <?
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
//        print_pre($row);
        echo '<td>'.@date("l jS \of F Y h:i:s A",strtotime($row['reschedule_datetime'])).'</td>';
        echo '<td><a href="rescheduled.php?call_number='.$row['phone_number'].'">'.$row['phone_number'].'</a></td>';
        echo '<td>'.$row['done'].'</td>';
        echo '</tr>';
    }
    echo '</table>';
}
echo '</div>';
require "footer.php";
?>