<?
require "header.php";
$result = mysqli_query($connection, "SELECT * FROM SmoothOperator.cdr where dst = 's' order by billsec desc limit 100000") or die(mysqli_error($connection));
if (mysqli_num_rows($result) > 0) {
    ?>
    <div class="thin_90perc_box">
    <table class="sample2" width="100%">
    <tr>
    <th>CallDate</th>
    <th>Source</th>
    <th>Destination</th>
    <th>Duration</th>
    <th>Billable Seconds</th>
    <th>Disposition</th>
    <th>Last App</th>
    <th>Phone Number</th>
    <th>Recording</th>
    </tr> 
    <?
    while ($row = mysqli_fetch_assoc($result)) {
//        print_pre($row);
        if ($row['dst'] == "500") {
            
            $result_x = mysqli_query($connection, "SELECT agent FROM SmoothOperator.queue_log where event = 'AGENTLOGIN' and callid = ".$row['uniqueid']);
            if (mysqli_num_rows($result_x) > 0) {
                while ($row_x = mysqli_fetch_assoc($result_x)) {
                    //echo '<td>'.$row_x['agent'].'</td>';
                    //$
                    $row['src'] = $row_x['agent'];
                    $row['dst'] = "Agent Login";
                }
            } else {
                $row['src'] = "Unknown";
                $row['dst'] = "Agent Login";
            }
        }
        echo '<tr>';
        echo '<td>'.$row['calldate'].'</td>';
        echo '<td>'.$row['src'].'</td>';
        echo '<td>'.$row['dst'].'</td>';
        echo '<td>'.$row['duration'].'</td>';
        echo '<td>'.$row['billsec'].'</td>';
        echo '<td>'.$row['disposition'].'</td>';
        echo '<td>'.$row['lastapp'].'</td>';
        if (strpos($row['userfield'],"-") !== false ) {
            $row['userfield'] = substr($row['userfield'],0,strpos($row['userfield'],"-"));
            $row['userfield'] = '<a href="rescheduled.php?call_number='.$row['userfield'].'">'.$row['userfield'].'</a>';

        }
        echo '<td>'.$row['userfield'].'</td>';
        if (file_exists('files/'.$row['uniqueid'].'.wav')) {
            echo '<td><a href="files/'.$row['uniqueid'].'.wav">'.$row['uniqueid'].'</a></td>';
        } else {
            echo '<td>'.$row['uniqueid'].'</td>';
        }
        echo '</tr>';
    }
    ?>
    </table>
    </div>
    <?
}
require "footer.php";
?>