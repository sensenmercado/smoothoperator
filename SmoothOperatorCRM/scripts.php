<?
/* 
 * The idea here is to load and process customer records using scripts. This
 * differs from normal displaying of customer records in much the same way as a
 * mail merge works.
 *
 * A script is a series of statements or questions that the call centre staff
 * member will read as they respond to a customer.  This way you can have staff
 * that are working simultaneously on multiple projects.  It can be useful for
 * outbound marketing as well as inbound calls as it allows for an easy way to
 * store information.
 *
 * A script is based on the following MySQL structure:
 *
 * id - autoincrement index (automatically created)
 * name - name of the script
 * description - description of the script
 * owner - the id of the user who created this script
 * lastupdated - automatically updated timestamp for last update
 * groupid - the id of a group of people allowed access to this script
 *
 * You then have script entries which are stored in the script_entries table:
 *
 * id - autoincrement index (automatically created)
 * script_id - id of the script that this refers to
 * type - the type of script entry i.e.:
 *       -1 - end of section/page
 *        0 - statement followed by text field
 *        1 - statement followed by a yes/no field
 *        2 - statement followed by a combo box field
 *        3 - statement followed by nothing
 * statement - the text to display
 * order - the position of the entry in the script as a whole
 *
 * If you are using a combo box field, the choices are supplied from the
 * script_choices table.  This table has the following structure:
 *
 * id - autoincrement index (automatically created)
 * script_id - id of the script that this refers to
 * text - the text of the choice (i.e. 'apple' or 'pear')
 * value - the value to be used when saving to database - i.e. without space
 *
 */

if (isset($_GET['save_field'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $url = parse_url($_POST['url']);
    $exploded = explode("=",$url['query']);
    $type = $exploded[0];
    $id = sanitize($exploded[1]);
    $field = sanitize($_POST['id'], false);
    $value = sanitize($_POST['new_value']);
    switch ($type) {
        case "edit":
            $sql = "UPDATE scripts SET $field = $value WHERE id = $id";
            $result = mysqli_query($connection, $sql);
            break;
        default:
            break;
    }
    
    $response['is_error'] = false;
    $response['error_string'] = mysqli_error($connection);;
    $response['html'] = $_POST['new_value'];
    echo json_encode($response);
    exit(0);
}


require "header.php";
if (isset($_GET['add'])) {
    ?>
    <br />
    <form action="scripts.php?edit_new=1" method="post">
    <table class="sample">
    <tr>
    <th>Script Name</th>
    <td><input type="text" name="name"></td>
    </tr>
    <tr>
    <th colspan="2">Description</th>
    </tr>
    <tr>
    <td colspan="2"><textarea name="description" rows="5"></textarea></td>
    </tr>
    <tr>
    <th colspan="2"><center><input type="submit" value="Add Script" style="width: 200px"></center></th>
    </tr>
    </table>
    </form>
    <?
    require "footer.php";
    exit(0);
}
if (isset($_GET['edit_new'])) {
    //TODO: make groupid work
    $result = mysqli_query($connection, "INSERT INTO scripts (name, description, owner, lastupdated, groupid) VALUES (".sanitize($_POST['name']).",".sanitize($_POST['description']).", ".sanitize($_SESSION['user_name']).",NOW(),0)") or die(mysqli_error($connection));
    $id =  mysqli_insert_id($connection);
    redirect("scripts.php?edit=$id",0);
    exit(0);
}
if (isset($_GET['edit'])) {
    $result = mysqli_query($connection, "SELECT * FROM scripts WHERE id = ".sanitize($_GET['edit']));
    if (mysqli_num_rows($result) == 0) {
        // Not found - shouldn't be possible
    } else {        
        $row = mysqli_fetch_assoc($result);
        ?>
        <script language="javascript">
        var counter = 0;
        function addInput(divName){
            counter++;
            var newdiv = document.createElement('div');
            newdiv.innerHTML = "Entry <br><input type='text' name='myInputs[]'>";
            document.getElementById(divName).appendChild(newdiv);
        }
        function add_end_of_section(divName){
            var newdiv = document.createElement('div');
            newdiv.innerHTML = "<hr />";
            document.getElementById(divName).appendChild(newdiv);
        }
        
        function add_statement_followed_by_text_field(statement, textfield_name, divName){
            counter++;
            var newdiv = document.createElement('div');
            newdiv.innerHTML = "<div class='script_input_entry'><b>"+counter+".</b> "+nl2br(statement)+" <br><input type='text' name='"+textfield_name+"'>";
            document.getElementById(divName).appendChild(newdiv);
        }
        
        function nl2br(dataStr) {
            return dataStr.replace(/(\r\n|\r|\n)/g, "<br />");
        }
        function display_adder() {
            /*
             *       -1 - end of section/page
             *        0 - statement followed by text field
             *        1 - statement followed by a yes/no field
             *        2 - statement followed by a combo box field
             *        3 - statement followed by nothing
             */
            switch (jQuery("#input_type option:selected").val()) {
                case '-1':
                    /* End of section/page */
                    add_end_of_section('dynamicInput');
                    break;
                case '0':
                    Dialog.confirm('Statement: <textarea id="statement_text"></textarea><br />Textfield Name (no spaces, unique): <input type="text" id="textfield_name">', {className:'alphacube', width:400, 
                                   okLabel: 'Add Section', cancelLabel: 'cancel',
                                   onOk:function(win){
                                   add_statement_followed_by_text_field(jQuery('#statement_text').val(), jQuery('#textfield_name').val(),'dynamicInput');
                                   return true;
                                   }
                                   }
                                   );
                    break;
                case '1':
                    Dialog.confirm($('new_section_1').innerHTML, {className:'alphacube', width:400, 
                                   okLabel: 'Add Section', cancelLabel: 'cancel',
                                   onOk:function(win){
                                   addInput('dynamicInput');
                                   return true;
                                   }
                                   }
                                   );
                    break;
                case '2':
                    Dialog.confirm($('new_section_2').innerHTML, {className:'alphacube', width:400, 
                                   okLabel: 'Add Section', cancelLabel: 'cancel',
                                   onOk:function(win){
                                   addInput('dynamicInput');
                                   return true;
                                   }
                                   }
                                   );
                    break;
                case '3':
                    Dialog.confirm($('new_section_3').innerHTML, {className:'alphacube', width:400, 
                                   okLabel: 'Add Section', cancelLabel: 'cancel',
                                   onOk:function(win){
                                   addInput('dynamicInput');
                                   return true;
                                   }
                                   }
                                   );
                    break;
                default:
                    break;
                    
            }
            
            
        }
        </script>
        <br />
        <table class="sample" width="400">
        <tr>
        <th>Script Name</th>
        <td><img src="images/pencil.png" align="right"><div id="name"><?=stripslashes($row['name'])?></div></td>
        </tr>
        <tr>
        <th>Description</th>
        <td><img src="images/pencil.png" align="right"><div id="description"><?=stripslashes($row['description'])?></div></td>
        </tr>
        </table>
        <br />
        <select name="input_type" id="input_type">
        <option value="0">statement followed by text field</option>
        <option value="1">statement followed by a yes/no field</option>
        <option value="2">statement followed by a combo box field</option>
        <option value="3">statement followed by nothing</option>
        <option value="-1">end of section/page</option>
        </select>
        
        <a href="#" onClick="display_adder();">Add Section</a>
        <br />
        <br />
        <input type="button" value="Save Script">
        <br />
        <br />
        
        <div id="dynamicInput" class="script_input_section" style="">
        <center><h3>Sample Script</h3></center>
        </div>
        <?/*
           <table class="sample">
           <tr>
           <td>Bla</td>
           </tr>
           </table>
           */?>
        

        <div id="new_section_1" style="display:none">
        Bla        
        <div style="clear:both"></div>
        </div>

        <div id="new_section_2" style="display:none">
        Bla        
        <div style="clear:both"></div>
        </div>

        <div id="new_section_3" style="display:none">
        Bla        
        <div style="clear:both"></div>
        </div>

        <script>
        jQuery( "#name" ).eip( "scripts.php?save_field=1" );
        jQuery( "#description" ).eip( "scripts.php?save_field=1" );
        
        
        
        
        </script>
        <?
        
    }
    require "footer.php";
    exit(0);
}
require "footer.php";

?>
