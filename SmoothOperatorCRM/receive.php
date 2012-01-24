<?
if (isset($_GET['save_list'])) {
    require "header.php";
    echo $_GET['option'];
    exit(0);
    $result = mysqli_query($connection, "SELECT location, filename, size, date_imported, id FROM files WHERE id = ".sanitize($_GET['save_list']));
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $filename = $row['location'];
    }
    require_once 'excel_reader2.php';
    /* conserve memory for large worksheets by not storing the extended */
    /* information about cells like fonts, colors, etc.                 */
    $data = new Spreadsheet_Excel_Reader($filename, false);
    $arr = $data->dumptoarray(0);
    //print_pre($_POST);
    for ($row = $_POST['first_row'];$row<=sizeof($arr);$row++) {
        $sql1 = "INSERT INTO customers (";
        $sql2 = " VALUES (";

        for ($col = 1;$col <= sizeof($arr[$row]);$col++) {
            if (isset($_POST['col_'.$col]) && $_POST['col_'.$col] != "null") {
                if ($_POST['col_'.$col] == "phone") {
                    $phone = $arr[$row][$col];
                }
                $sql1.= sanitize($_POST['col_'.$col], false ).",";
                $sql2.= sanitize($arr[$row][$col]).",";
            }
            //echo "x".$_POST['col_'.$col].": ".$arr[$row][$col]."<br />";
        }
        $sql1.="cleaned_number) ";
        $sql2.=clean_number($phone).")";
        $sql = $sql1.$sql2;
        //echo "<!-- -->$sql<br />";
        $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    }
    redirect("receive.php");
    require "footer.php";
    exit(0);
}
if (isset($_GET['import_list'])) {
    if (!isset($_GET['option'])) {
        require "header.php";

        ?>
        <div class="thin_700px_box">
            <a href="receive.php?import_list=<?=$_GET['import_list']?>&option=new">Create New List</a><br />
            <a href="receive.php?import_list=<?=$_GET['import_list']?>&option=existing">Import Into Existing List</a><br />
            <a href="receive.php?import_list=<?=$_GET['import_list']?>&option=split">Split Into Multiple Lists</a><br />
        </div>
        <?
        require "footer.php";
        exit(0);
    } else if (!isset($_GET['list_id'])) {
        // List ID not specified
        switch ($_GET['option']) {
                case "new":
                break;
                case "existing":
                break;
                case "split":
                break;
        }
    } else {
        require "header.php";
        ?>
        <form action="receive.php?save_list=<?=$_GET['import_list']?>&option=<?=$_GET['option']?>" method="POST">
        First Row: <select name="first_row">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>

        </select>
        <?
        if ($_GET['option'] == "new") {
            ?>
            <?
        }

        $result = mysqli_query($connection, "SELECT location, filename, size, date_imported, id FROM files WHERE id = ".sanitize($_GET['import_list']));
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $filename = $row['location'];
        }
        require_once 'excel_reader2.php';
        /* conserve memory for large worksheets by not storing the extended */
        /* information about cells like fonts, colors, etc.                 */
        $data = new Spreadsheet_Excel_Reader($filename, false);
        $arr = $data->dumptoarray(0);
        //$max = sizeof($arr);
        $max = 5;
        $result = mysqli_query($connection, "SHOW COLUMNS FROM customers");
        $fields_to_ignore[] = "id";
        $fields_to_ignore[] = "new";
        $fields_to_ignore[] = "last_updated";
        $fields_to_ignore[] = "locked_by";
        $fields_to_ignore[] = "datetime_locked";
        $fields_to_ignore[] = "cleaned_number";
        $fields_to_ignore[] = "list_id";

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (!in_array($row['Field'], $fields_to_ignore)) {
                    $fields[] = $row['Field'];
                }
            }
        }
        
        echo '<table class="sample">';
        $printed_header = false;
        /* Row and column offsets are 1 */
        for ($row = 1;$row <= $max;$row++) {
            if (!$printed_header) {
                $printed_header = true;
                echo '<tr>';
                echo '<th>Row</th>';
                for ($col = 1;$col <= sizeof($arr[$row]);$col++) {
                    echo '<th><select name="col_'.$col.'"><option value="null">Not Used</option>';
                    foreach ($fields as $field) {
                        echo '<option value="'.$field.'">'.clean_field_name($field).'</option>';
                    }
                    echo '</select></th>';
                }
                echo '</tr>';
            }
            echo "<tr>";
            echo "<td>$row</td>";
            for ($col = 1;$col <= sizeof($arr[$row]);$col++) {
                echo "<td>".$arr[$row][$col]."</td>";;
            }
            echo "</tr>";
        }
        echo "</table>";
        ?>
        <input type="submit" value="Import List">
        </form>
        <?
        require "footer.php";
        exit(0);
    }
}
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
<div class="thin_700px_box">
<table>
<tr>
<td>
<font size="3">Select a file to upload:</font>
</td>
<td>
<input id="fileInput" name="fileInput" type="file" />
<script type="text/javascript">// <![CDATA[
jQuery(document).ready(function() {
jQuery('#fileInput').uploadify({
'uploader'  : 'swf/uploadify.swf',
'script'    : 'uploadify.php',
'cancelImg' : 'cancel.png',
'auto'      : true,
'sizeLimit' : '100000000',
'scriptAccess': 'always',
'onComplete'  : myfunc,
'folder'    : 'uploads-folder/'
});
});
// ]]>
setInterval(myfunc, 30000);
myfunc();
function myfunc() {
    jQuery("#contentx").load("view_files.php");
}
</script>
</td>
</tr>
</table>
</div>
<div class="thin_700px_box">

<div id="contentx"><img src="images/sq_progress.gif">&nbsp;Please Wait...</div>
</div>
<script type="text/javascript">
    myfunc();
    </script>

<?
require "footer.php";
?>