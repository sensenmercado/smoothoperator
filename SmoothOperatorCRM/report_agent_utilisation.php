<?
require "header.php";
function mysqli_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
}
if (!isset($_POST['range'])) {
    echo "<br />";
    box_start(500);
    ?><center>
    <h3>Search for utilisation</h3>
        <form action = "report_agent_utilisation.php" method="post">
        Agent: <select name="user_id">
        <?
        $result = mysqli_query($connection, "SELECT id, first_name, last_name FROM users");
    while ($row = mysqli_fetch_assoc($result)) {
        print_pre($row);
        echo '<option value="'.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].'</option>';
    }
    ?>
    </select><br />

    Time Period:<select name="range" id="range">
    <option value="alltime">All Time</option>
    <option value="today">Today</option>
    <option value="date">Select Date</option>
    </select><br />
    <div id="dateselect" style="display:none">
    <p>From Date:
    <input type="text" id="from_date" name="from_date" style="width: 200px">
    <br />
    To Date:
    <input type="text" id="to_date"  name="to_date" style="width: 200px">
    <br />
    
    </div>
    <input type="submit" value="Display Results"><br />
    <br />
    </form>
    <script>
    jQuery(function() {
           jQuery( "#from_date" ).datepicker({
                                             dateFormat : 'yy-mm-dd'
                                             });
           jQuery( "#to_date" ).datepicker({
                                           dateFormat : 'yy-mm-dd'
                                           });
           
           });
    jQuery(function() {    // Makes sure the code contained doesn't run until
           //     all the DOM elements have loaded
           
           jQuery('#range').change(function(){
                                   //alert("x");
                                   //alert($('#range').val());
                                   if (jQuery('#range').val() == "date") {
                                   jQuery('#dateselect').show();
                                   } else {
                                   jQuery('#dateselect').hide();
                                   
                                   }
                                   
                                   });
           });
    </script>
    
    <?
    box_end();
    require "footer.php";
    exit(0);
}
switch ($_POST['range']) {
    case "today":
        $sql = "select count(*), hour(contact_date_time) from customer_dispositions where user_id = ".sanitize($_POST['user_id'])." and date(contact_date_time) = CURDATE() group by hour(contact_date_time)";
        $title = 'Dispositions '.ucwords($name).' Today';
        break;
    case "date":
        $sql = "select count(*), hour(contact_date_time) from customer_dispositions where user_id = ".sanitize($_POST['user_id'])." and date(contact_date_time) between ".sanitize($_POST['from_date'])." and ".sanitize($_POST['to_date'])." group by hour(contact_date_time)";
        $title = 'Dispositions '.ucwords($name).' Between '.$_POST['from_date'].' and '.$_POST['to_date'];
        break;
    default:
        $sql = "select count(*), hour(contact_date_time) from customer_dispositions where user_id = ".sanitize($_POST['user_id'])." group by hour(contact_date_time)";
        $title = 'Dispositions '.ucwords($name).' All Time';
        break;
}
$values = array();
for ($time = 0;$time<24;$time++) {
    $values[$time] = 0;
}
$result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
while ($row = mysqli_fetch_assoc($result)) {
    $values[$row['hour(contact_date_time)']] = $row['count(*)'];
}
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {packages: ["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
    
    var data = new google.visualization.DataTable();
    
    
    var data = google.visualization.arrayToDataTable([
                                                     ['Disposition', 'Count'],
                                                     <?
                                                     foreach ($values as $hour=>$count) {
                                                     echo "['".ucwords($hour).":00', ".$count."],\n";
                                                     }
                                                     ?>
                                                     
                                                     ]);
    
    
    
    
    var options = {
    title: '<?=$title?>'
    };
    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
</script>
<div id="chart_div" style="width: 900px; height: 500px;"></div>
<?
require "footer.php";
?>