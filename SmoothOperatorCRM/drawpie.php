<?
require "header.php";
/*$result = mysqli_query($connection, "SELECT unix_timestamp(concat(report_date,' ',report_time)) as date, new, answered, busy, congested, amd, unknown, pressed1, hungup, timeout FROM campaign_stats WHERE campaign_id = 100002") or die(mysqli_error($connection));
$text = "";
while ($row = mysqli_fetch_assoc($result)) {
    $text .= "[".$row['date'].",  ".$row['new'].",".$row['answered'].",".$row['busy'].",".$row['congested'].",".$row['amd'].",".$row['unknown'].",".$row['pressed1'].",".$row['hungup'].",".$row['timeout']."],<br />";
}
echo substr($text,0,-1);
exit(0);*/
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {packages: ["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
    
    var data = new google.visualization.DataTable();
    data.addColumn('datetime', 'Date');
    data.addColumn('number', 'Timeout');
    data.addColumn('number', 'Answered');
    data.addColumn('number', 'Busy');
    data.addColumn('number', 'Congested');
    data.addColumn('number', 'Answer Machine');
    data.addColumn('number', 'Unknown');
    data.addColumn('number', 'New');
    
    <?
    $result = mysqli_query($connection, "SELECT unix_timestamp(concat(report_date,' ',report_time)) as date, new, answered, busy, congested, amd, unknown, pressed1, hungup, timeout FROM campaign_stats WHERE campaign_id = 100003") or die(mysqli_error($connection));
    $text = "";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "data.addRows([[new Date(".$row['date']."000), ".$row['timeout'].", ".($row['answered']+$row['pressed1']+$row['hungup']).", ".$row['busy'].", ".$row['congested'].", ".$row['amd'].", ".$row['unknown'].", ".$row['new']."]]);\n";
//        echo "data.addRows(['".$row['date']."',".$row['timeout'].",".($row['answered']+$row['pressed1']+$row['hungup']).",".$row['busy'].",".$row['congested'].",".$row['amd'].",".$row['unknown'].",".$row['new']."]);\n";
    }
//    echo substr($text,0,-1);
    ?>
    
    
    
       var options = {
    title: 'Number Status',
    areaOpacity: 1,
    colors: ['#444444','#88ff88','#0000ff','#ff0000','#ffff00','#888888','#eeffee'],
    isStacked: true,
    hAxis: {title: 'Time',  titleTextStyle: {color: 'red'}}
    };
    
    var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
</script>
<div id="chart_div" style="width: 900px; height: 500px;"></div>
<?
require "footer.php";
?>