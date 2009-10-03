<?
//phpinfo();
//exit(0);
if (isset($_GET['delete'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $result = mysqli_query($connection, "SELECT location FROM files where id = ".sanitize($_GET['delete']));
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            unlink($row['location']);
        }
    }

    $result = mysqli_query($connection, "DELETE FROM files where id = ".sanitize($_GET['delete']));
}

if (isset($_GET['id'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $result = mysqli_query($connection, "SELECT * FROM files where id = ".sanitize($_GET['id']));
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            header("Content-length: ".$row['size']);
            header("Content-type: ".$row['type']);
            header("Content-Disposition: attachment; filename=".$row['filename']);
            //include ($row[''])
            $fp      = fopen($row['location'], 'r');
            $content = fread($fp, filesize($row['location']));
            //$content = ($content);
            fclose($fp);
            echo $content;
        }
    }
    //require "footer.php";
    exit(0);
}

require "header.php";


//echo "Time:".time();
?>
<div class="xxxx" align="right" style="background: #cdf;width: 600px;margin-top: 30px;padding:10px;">
<table>
<tr>
<td>
<font size="3">Select a file to upload:</font>
</td>
<td>
<input id="fileInput" name="fileInput" type="file" />
<script type="text/javascript">// <![CDATA[
$(document).ready(function() {
$('#fileInput').uploadify({
'uploader'  : 'swf/uploadify.swf',
'script'    : 'uploadify.php',
'cancelImg' : 'cancel.png',
'auto'      : true,
'sizeLimit' : '100000000',
'scriptAccess': 'always',
'folder'    : 'uploads-folder/'
});
});
// ]]>
setInterval(myfunc, 2000);
myfunc();
function myfunc() {
    $("#contentx").load("view_files");
}
</script>
</td>
</tr>
</table>
</div>
<div class="xxxx"  style="background: #cdf;width: 600px;margin-top: 30px;padding:10px;">

<div id="contentx"><img src="images/sq_progress.gif">&nbsp;Please Wait...</div>
</div>
<?
require "footer.php";
?>