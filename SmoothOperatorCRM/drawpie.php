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
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
    var data = google.visualization.arrayToDataTable
    ([
     ['Date', 'New', 'Answered', 'Busy', 'Congested', 'Answer Machine', 'Unknown', 'Pressed 1', 'Hungup', 'Timeout'],
     <?
     $result = mysqli_query($connection, "SELECT (concat(report_date,' ',report_time)) as date, new, answered, busy, congested, amd, unknown, pressed1, hungup, timeout FROM campaign_stats WHERE campaign_id = 100003") or die(mysqli_error($connection));
     $text = "";
     while ($row = mysqli_fetch_assoc($result)) {
        $text .= "['".$row['date']."',  ".$row['new'].",".$row['answered'].",".$row['busy'].",".$row['congested'].",".$row['amd'].",".$row['unknown'].",".$row['pressed1'].",".$row['hungup'].",".$row['timeout']."],";
     }
     echo substr($text,0,-1);
     ?>
     ]);
    
    var options = {
    title: 'Number Status',
    isStacked: true,
    hAxis: {title: 'Year',  titleTextStyle: {color: 'red'}}
    };
    
    var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
</script>
<div id="chart_div" style="width: 900px; height: 500px;"></div>
<?
require "footer.php";
?>