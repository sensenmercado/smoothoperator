<?
if (isset($_GET['create_list'])) {
    
    require "config/db_config.php";
    require "functions/sanitize.php";
    $result = mysqli_query($connection, "INSERT INTO lists (name, description) VALUES (".sanitize($_POST['list_name']).",".sanitize($_POST['list_description']).")");
    $new_id = mysqli_insert_id($connection);
    echo $new_id;
    exit(0);
}
if (isset($_GET['save_list'])) {
    require "header.php";
    //print_pre($_POST);
    //echo $_GET['option'];
    //exit(0);
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
        $sql1.="cleaned_number,list_id) ";
        $sql2.="'".clean_number($phone)."',".sanitize($_POST['list_id']).")";
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
        Where would you like to import them to?<br />
        <form action="receive.php" method="get">
        <select id="list_type" name="option" onchange="choose_list(this);">
        <option value="none">Please select an entry</option>
        <option value="new">Create a new list</option>
        <option value="existing">Import into existing list</option>
        
        </select>
        </div>
        <input type="hidden" name="list_id" id="list_id">
        </form>
        <div id="new_list" style="display: none" title="New List">
        <form id="new_list_form">
        Name: <input type="text" name="list_name" id="list_name" >
    Description: <br /><textarea name="list_description" id="list_description"></textarea>
        </form>
        </div>
        <div id="existing_list" style="display: none">
        <?
        $result = mysqli_query($connection, "SELECT * FROM lists");
        if (mysqli_num_rows($result) == 0) {
            ?>Sorry there are no existing lists...<br />
            <br />
            <script>
            jQuery('#existing_list').dialog('option', 'buttons', {"bla"}); 
            </script>
            <?
            
        } else {
            ?>
            Please Select Your List:<br />
            <br />
            <select name="list" id="existing_list_id">
            <?
            while($row = mysqli_fetch_assoc($result)) {
                echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
            }
            ?>
            </select><br />
            <br />
            <?
        }
        ?>
        </div>
        <script>
        function choose_list() {
            var type = jQuery("#list_type").val();
            if (type == "new") {
                jQuery("#new_list").dialog({
                                           modal: true, 
                                           width:'auto',
                                           buttons: {
                                           "Add List": function() {
                                           
                                           
                                           
                                           
                                           jQuery.post('receive.php?create_list=1', jQuery("#new_list_form").serialize(), function(data) {
                                                       //alert(data);
                                                       window.location.href="receive.php?import_list=1&option=new&list_id="+data;
                                                       
                                                       });
                                           
                                           
                                           jQuery(this).dialog("close");
                                           
                                           }
                                           }
                                           });
            } else if (type == "existing") {
                jQuery("#existing_list").dialog({modal: true,
                                                width:'auto',
                                                buttons: {
                                                "Select List": function() {
                                                
                                                            window.location.href="receive.php?import_list=1&option=existing&list_id="+jQuery("#existing_list_id").val();                                                           
                                                
                                                
                                                jQuery(this).dialog("close");
                                                
                                                }
                                                }
                                                });
            }
            
        }
        </script>
        <?
        require "footer.php";
        exit(0);
    } else if (!isset($_GET['list_id'])) {
        // List ID not specified
        ?>
        <form action=
        <?
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
        <input type="hidden" name="list_id" value="<?=$_GET['list_id']?>">
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