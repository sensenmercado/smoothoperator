<?
if (isset($_GET['reschedule_number'])) {
    require "header.php";
    $sql = "INSERT INTO reschedule (phone_number, reschedule_datetime, user) VALUES (".sanitize($_GET['phone_number']).",".sanitize($_GET['date']." ".$_GET['time']).", ".$_SESSION['user_id'].")";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    redirect("get_customer.php?from=".$_GET['from']."&phone_number=".$_GET['phone_number'],3,"Rescheduling a call for ".$_GET['time']." on ".$_GET['date']);
    require "footer.php";
    exit(0);
}
if (isset($_GET['save_script'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    /*    $sql = "INSERT INTO customers (`phone`, `cleaned_number`) VALUES (".sanitize($_POST['phonenumber']).",".sanitize(clean_number($_POST['phonenumber'])).")";
     $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
     $new_id = mysqli_insert_id($connection);
     echo $new_id;
     */
    foreach ($_GET as $field=>$value) {
        if (strlen($field) > 5 && substr($field, 0, 5) == "field") {
            $fieldnum = substr($field,5);
            echo $fieldnum.":".$value."\n";
            $sql = "REPLACE INTO script_results (customer_id, script_id, question_number, answer) VALUES (".sanitize($_GET['customer_id']).",".sanitize($_GET['script_id']).",".sanitize($fieldnum).",".sanitize($value).")";
            mysqli_query($connection, $sql) or die(mysqli_error($connection));
        }
    }
    echo "Done";
    exit(0);
}
if (isset($_GET['save_record'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $sql = "INSERT INTO customers (`phone`, `cleaned_number`) VALUES (".sanitize($_POST['phonenumber']).",".sanitize(clean_number($_POST['phonenumber'])).")";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    $new_id = mysqli_insert_id($connection);
    echo $new_id;
    exit(0);
}
if (isset($_GET['save_disposition'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $sql = "INSERT INTO customer_dispositions (contact_date_time, disposition, user_id, extension, customer_id) VALUES (NOW(), ".sanitize($_POST['disposition']).", ".sanitize($_POST['user_name']).", ".sanitize($_POST['extension']).", ".sanitize($_POST['id']).")";
    //$result = mysqli_query($connection, $sql);
    
    //$sql = "INSERT INTO customers (`phone`) VALUES (".sanitize($_POST['phonenumber']).")";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    //$new_id = mysqli_insert_id($connection);
    //echo $sql;
    //    echo "x";
    //echo $new_id;
    exit(0);
}
$rounded[] = "div.messages";
$rounded[] = "div.thin_700px_box";

require "header.php";
if (isset($_GET['save'])) {
    /* Saving a customer record */
    if (isset($_POST['new'])) {
        /* This is a new entry */
        $fields_to_ignore[] = "new";
        $sql1 = "INSERT INTO customers (";
        $sql2 = "VALUES (";
        foreach ($_POST as $field=>$value) {
            if (!in_array($field, $fields_to_ignore)) {
                $sql1.=sanitize($field, false).",";
                $sql2.=sanitize($value, true).",";
            }
        }
        $clean = clean_number($_POST['phone']);
        
        $sql = $sql1."cleaned_number) ".$sql2.sanitize($clean).")";
        $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
        redirect("list_customers.php");
    } else {
        /* This is an update of an existing entry */
        $sql = "UPDATE customers SET ";
        $fields_to_ignore[] = "id";
        $fields_to_ignore[] = "new";
        $fields_to_ignore[] = "last_updated";
        $fields_to_ignore[] = "locked_by";
        $fields_to_ignore[] = "datetime_locked";
        
        foreach ($_POST as $field=>$value) {
            /* Only update fields which are not id, last updated or new */
            if (!in_array($field, $fields_to_ignore)) {
                if ($field == "cleaned_number") {
                    /* Remove any crap from the number - i.e. anything but numbers */
                    $value = clean_number($_POST['phone']);
                }
                $sql.= " ".sanitize($field,false)." = ".sanitize($value).",";
            }
        }
        /* Strip the comma */
        $sql = substr($sql,0,strlen($sql)-1);
        $sql.= " WHERE id = ".sanitize($_POST['id']);
        $result = mysqli_query($connection, $sql);
        redirect("list_customers.php");
    }
    require "footer.php";
    exit(0);
}
if (!isset($_GET['phone_number'])) {
    redirect("list_customers.php");
    exit(0);
}
if (!is_numeric($_GET['phone_number'])) {
    $_GET['phone_number'] = preg_replace('/[^0-9]/',"",$_GET['phone_number']);
}
function display_script($customer) {
    global $connection;
    ?>
    <script>
    
    
    var entries_to_ids=new Array();
    var counter = 0;
    function addInput(divName){
        counter++;
        jQuery("#"+divName).append("Entry <br><input type='text' name='myInputs[]'>");
    }
    function add_end_of_section(divName){
        counter++;
        jQuery("#"+divName).append("<hr />");
    }
    
    /* Statement followed by text field */
    
    function add_statement_followed_by_text_field(statement, divName){
        counter++;
        jQuery("#"+divName).append(nl2br(statement)+" <br><input type='text' name='field"+counter+"'><br />");
    }
    
    /* Statement followed by yes/no */
    
    function add_statement_followed_by_yesno(statement, divName){
        counter++;
        jQuery("#"+divName).append(nl2br(statement)+" <br><select name='field"+counter+"'><option value='YES'>Yes</option><option value='NO'>No</option></select><br />");
    }
    
    function add_statement_followed_by_combobox(statement, comboboxes, divName){
        counter++;
        var ih = nl2br(statement)+" <br><select name='field"+counter+"'>";
        comboboxes.each(function(){
                        ih += "<option value='"+jQuery(this).val()+"'>"+jQuery(this).val()+"</option>";
                        });
        ih += "</select>";
        jQuery("#"+divName).append(ih+"<br />");
    }
    
    function add_statement_followed_by_combobox_from_db(statement, comboboxes, divName){
        counter++;
        var ih = nl2br(statement)+" <br><select name='field"+counter+"'>";
        comboboxes.forEach(function(item){ih += "<option value='"+item+"'>"+item+"</option>";});
        ih += "</select>";
        jQuery("#"+divName).append(ih+"<br />");
    }
    
    /* Statement followed by nothing */
    
    function add_statement_followed_by_nothing(statement, divName){
        counter++;
        jQuery("#"+divName).append(nl2br(statement)+"<br />");
    }
    function nl2br(dataStr) {
        return dataStr.replace(/(\r\n|\r|\n)/g, "<br />");
    }
    </script>
    <br />
    <div id="dynamicInput3" class="script_input_section" style="">
    <form action="xxx.php" method="post" id="script_form">
    <span id="dynamicInput2"></span>
    </form>
    </div>
    
    <?
    $sql = "SELECT scripts.id FROM scripts, job_members, jobs  WHERE scripts.id = jobs.script_id and job_members.job_id = jobs.id and job_members.user_id = ".sanitize($_SESSION['user_id']);
    $result_script = mysqli_query($connection, $sql);
    $row_script = mysqli_fetch_assoc($result_script);
    $script_id = $row_script['id'];
    $result_entries = mysqli_query($connection, "SELECT * FROM script_entries WHERE script_id = ".$script_id);
    $x = 0;
    if (mysqli_num_rows($result_entries) > 0) {
        while ($row_entries = mysqli_fetch_assoc($result_entries)) {
            $row_entries['statement'] = str_replace("{first_name}","<b>".$customer['first_name']."</b>",$row_entries['statement']);
            $row_entries['statement'] = str_replace("{agent}","<b>".$_SESSION['name']."</b>",$row_entries['statement']);
            
            $x++;
            ?>
            <script language="javascript">
            entries_to_ids[<?=$x?>] = <?=$row_entries['id']?>;
            jQuery("#dynamicInput2").append('<input type="hidden" name="script_id" value="<?=$script_id?>">');
            
            </script>
            <?
            switch ($row_entries['type']) {
                case 0:
                    ?>
                    <script>add_statement_followed_by_text_field(<?=stripslashes(sanitize($row_entries['statement']))?>, 'dynamicInput2');</script>
                    <?
                    break;
                case 1:
                    ?>
                    <script>add_statement_followed_by_yesno(<?=stripslashes(sanitize($row_entries['statement']))?>, 'dynamicInput2');</script>
                    <?
                    break;
                case 2:
                    ?>
                    <script>
                    var combobox_entries=new Array();
                    <?
                    $sql = "SELECT * FROM script_choices WHERE script_entry_id = ".$row_entries['id'];
                    $result_comboboxes = mysqli_query($connection, $sql);
                    if (mysqli_num_rows($result_comboboxes) > 0) {
                        //echo "alert('$sql');";
                        $count = 0;
                        while ($row_comboboxes = mysqli_fetch_assoc($result_comboboxes)) {
                            $string = 'combobox_entries['.$count.'] = "'.$row_comboboxes['text'].'";';
                            echo $string;
                            $count++;
                        }
                    } else {
                        //echo "alert('No results from $sql');";
                    }
                    ?>
                    add_statement_followed_by_combobox_from_db(<?=stripslashes(sanitize($row_entries['statement']))?>, combobox_entries, 'dynamicInput2');
                    </script>
                    <?
                    break;
                case 3:
                    ?>
                    <script>add_statement_followed_by_nothing(<?=stripslashes(sanitize($row_entries['statement']))?>, 'dynamicInput2');</script>
                    <?
                    break;
                case -1:
                    ?>
                    <script>add_end_of_section('dynamicInput2');</script>
                    <?
                    break;
            }
            
        }
    }
    
    ?>
    <script>
    jQuery("#dynamicInput2").append("</form>");
    </script>
    
    <?
}

function display_dispositions() {
    global $connection;
    if (!isset($_GET['disposition_set'])) {
        if (!isset($_GET['user_id'])) {
            $_GET['user_id'] = $_SESSION['user_id'];
        }
        
        $sql = "SELECT * FROM job_members, jobs WHERE job_members.job_id = jobs.id and job_members.user_id = ".sanitize($_GET['user_id']);
        $result = mysqli_query($connection, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $row2 = mysqli_fetch_assoc($result);
            $result_dispositions = mysqli_query($connection, "SELECT * FROM job_dispositions WHERE job_id = ".$row2['job_id']);
            if (mysqli_num_rows($result_dispositions) == 0) {
                
            } else {
                $x = 0;
                echo "<table id=\"dispositions\">";
                $closed = false;
                
                // Add disposition for rescheduling a call
                echo "<tr>";
                $x++;
                echo "<td>";
                
                echo '<a href="javascript:void(0)" onclick="reschedule();">';
                
                box_start(145);
                echo "<center>";
                ?>
                <img src = "images/clock.png" alt="Reschedule Call">
                <?
                echo "Reschedule Call";
                box_end();
                echo '</a>';
                echo "</td>";
                
                
                
                
                while ($rowx = mysqli_fetch_assoc($result_dispositions)) {
                    if ($x == 0) {
                        echo "<tr>";
                        $closed = false;
                    }
                    $x++;
                    echo "<td>";
                    
                    /* When you click a dispostion you need to do the following:
                     
                     1. Check the current record is in the database ($row['new'] != 1)
                     2. Add an interraction for the current record
                     3. Remove the dispositions
                     
                     */
                    
                    echo '<a href="#" onclick="save_disposition(\''.$rowx['id'].'\');jQuery(\'#dispositions\').fadeOut(500);jQuery(\'#status_bar\').text(\'Disposition set to '.$rowx['text'].'\');jQuery(\'#status_bar\').fadeIn(2000);jQuery(\'#status_bar\').fadeOut(5000);    ">';
                    
                    box_start(135);
                    echo "<center>";
                    echo $rowx['text'];
                    box_end();
                    echo '</a>';
                    
                    
                    
                    
                    echo "</td>";
                    if ($x > 5) {
                        $x = 0;
                        echo "</tr>";
                        $closed = true;
                    }
                    //echo '</div></div>';
                }
                
                
                if (!$closed) {
                    echo "</tr>";
                }
                echo "</table>";
            }
            
        } else {
            box_start();
            echo "You are not currently assigned to a job";
            box_end();
        }
    }
    
}
function display_customer_edit($row) {
    global $connection;
    //print_pre($row);
    ?>

    <?
    if ($row['new'] == 1) {
        ?>
        <script>
        window.newID = 0;
        function save_disposition(disposition){
            new Ajax.Request('get_customer.php?save_record=1',{parameters: {phonenumber: <?=$_GET['phone_number']?>}, onSuccess: function(transport){
                             if (transport.responseText) {
                             var response = transport.responseText;
                             window.newID = parseInt(response);
                             //alert(response);
                             
                             new Ajax.Request('get_customer.php?save_script=1&customer_id='+window.newID+"&"+jQuery("#script_form").serialize(),{onSuccess: function(transport){
                                              if (transport.responseText) {
                                              var response = transport.responseText;
                                              jQuery('#dynamicInput3').fadeOut(3000);
                                              //alert(response);
                                              }
                                              }
                                              });
                             
                             
                             new Ajax.Request('get_customer.php?save_disposition=1',{parameters: {id: window.newID, disposition: disposition, user_name: "<?=$_SESSION['user_name']?>", extension: "<?=$_SESSION['extension']?>"}, onSuccess: function(transport){
                                              if (transport.responseText) {
                                              var response = transport.responseText;
                                              //entries_to_ids[counter] = parseInt(response);
                                              window.location="get_customer.php?phone_number=<?=$_GET['phone_number']?>&disposition_set=1";
                                              }
                                              }
                                              });
                             }
                             
                             }
                             });
            
            
        }
        
        
        
        
        
        
        </script>
        <?
    } else {
        ?>
        <script>
        
        function save_disposition(disposition){
            
            new Ajax.Request('get_customer.php?save_disposition=1',{parameters: {id: <?=$row['id']?>, disposition: disposition, user_name: "<?=$_SESSION['user_name']?>", extension: "<?=$_SESSION['extension']?>"}, onSuccess: function(transport){
                             if (transport.responseText) {
                             var response = transport.responseText;
                             //entries_to_ids[counter] = parseInt(response);
                             jQuery('#status_bar').text("Saved Disposition");
                             jQuery('#status_bar').fadeIn(1000);
                             jQuery('#status_bar').fadeOut(5000);
                             //alert(response);
                             }
                             }
                             });
            new Ajax.Request('get_customer.php?save_script=1&customer_id='+<?=$row['id']?>+'&'+jQuery("#script_form").serialize(),{onSuccess: function(transport){
                             if (transport.responseText) {
                             var response = transport.responseText;
                             jQuery('#dynamicInput3').fadeOut(3000);
                             //alert(response);
                             }
                             }
                             });
            
        }
        </script>
        <?
    }
    
    ?>
    <script>
    
    
    function reschedule(){
        
        <?
        /*foreach ($_GET as $field=>$value) {
            ?>alert('<?=$field."=".$value?>');<?
        }*/
        ?>
        jQuery("#content").append('<div id="reschedule" style="display: none"><center><form id="reschedule_form">Date: <input id="date-picker" name="date-picker"><br />Time: <input type="text" id="time-picker" name="time-picker" value="<?=@date("H:i")?>" style="width: 50px"><input id="done" type="submit" value="Reschedule Call"></form></div>');
        jQuery('#date-picker').datepicker({
                                     dateFormat : 'yy-mm-dd'
                                     });
        jQuery("#reschedule").dialog();
        jQuery("#reschedule_form").submit(function(e) {
                                          e.preventDefault();
                                          window.location = "get_customer.php?reschedule_number=1&phone_number=<?=$_GET['phone_number']?>&from=list&date="+jQuery("#date-picker").val()+"&time="+jQuery("#time-picker").val();
                                          //return("false");
                                          //alert("Redirected");
                                          //jQuery("#reschedule").close();
                                          });
    }

    </script>
    <?
    
    echo '<div class="thin_700px_box">';
    
    $fields_to_hide[] = "id";
    $fields_to_hide[] = "cleaned_number";
    $fields_to_hide[] = "last_updated";
    $fields_to_hide[] = "status";
    $fields_to_hide[] = "locked_by";
    $fields_to_hide[] = "datetime_locked";
    $fields_to_hide[] = "list_id";
    $fields_to_hide[] = "job_id";
    $fields_to_hide[] = "new";
    $textarea_fields[] = "notes";
    echo '<form action="get_customer.php?save=1" method="post">';
    echo '<table class="sample">';
    foreach ($row as $field=>$value) {
        if (in_array($field, $fields_to_hide)) {
            echo '<input type="hidden" name="'.$field.'" value="'.@stripslashes($value).'">';
        } else if (in_array($field, $textarea_fields)) {
            echo '<tr><th colspan="2">'.clean_field_name($field).'</th></tr>';
            echo '<tr><td colspan="2"><textarea cols="60" rows="10" name="'.$field.'">'.stripslashes($value).'</textarea></td></tr>';
        } else {
            echo '<tr><th>'.clean_field_name($field).'</th><td><input type="text" name="'.$field.'" value="'.stripslashes($value).'"></td></tr>';
        }
    }
    echo '<tr><td colspan="2"><input type="submit" value="save changes"></td></tr>';
    echo '</form>';
    echo "</table>";
    echo "</div>";
}
?>
<?
$phone_number = clean_number($_GET[phone_number]);
$result = mysqli_query($connection, "SELECT * FROM SmoothOperator.customers WHERE cleaned_number = '$phone_number'");
if (mysqli_num_rows($result) > 0) {
    if (1||mysqli_num_rows($result) == 1) {
        // Single Row Found
        $row = mysqli_fetch_assoc($result);
        
        $sql = "SELECT job_id FROM job_members WHERE user_id = ".sanitize($_SESSION['user_id']);
        //echo $sql;
        $resultx = mysqli_query($connection, $sql) or die(mysqli_error($connection));
        if ($resultx && mysqli_num_rows($resultx) > 0) {
            $row_temp = mysqli_fetch_assoc($resultx);
            $row['job_id'] = $row_temp['job_id'];
        } else {
            $row['job_id'] = "-1";
        }
        
        if (isset($_GET['pop'])) {
            $result = mysqli_query($connection, "INSERT INTO interractions (contact_date_time, notes, customer_id) VALUES (NOW(), 'Number screen popped to ".$_SESSION['user_name']." on extension: ".$_SESSION['extension']."', ".$row['id'].")");
            $_SESSION['calls']++;
        } else {
            $result = mysqli_query($connection, "INSERT INTO interractions (contact_date_time, notes, customer_id) VALUES (NOW(), 'Opened by ".$_SESSION['user_name']." on extension: ".$_SESSION['extension']."', ".$row['id'].")");
            
        }
        display_script($row);
        display_dispositions();
        display_customer_edit($row);
        
        /* Display interractions */
        
        if (isset($_GET['interractions'])) {
            $result = mysqli_query($connection, "SELECT * FROM interractions WHERE customer_id = ".$row['id']." ORDER BY contact_date_time desc");
            if (mysqli_num_rows($result) > 0) {
                echo '<br /><table class="sample">';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr><th>Date: </th><td><b>'.$row['contact_date_time'].'</b></td></tr>';
                    echo '<tr><th>Notes: </th><td>'.$row['notes'].'</td></tr><tr><th colspan="2"></th></tr>';
                }
                echo '</table>';
            }
        }
    } else {
        // Multiple Rows Found
        
        // TODO: FILL THIS OUT
        
    }
} else {
    unset($row);
    $row['first_name'] = "";
    $row['last_name'] = "";
    $row['address_line_1'] = "";
    $row['address_line_2'] = "";
    $row['city'] = "";
    $row['state'] = "";
    $row['zipcode'] = "";
    $row['email'] = "";
    $row['phone'] = $_GET['phone_number'];
    //$row['cleaned_number'] = $_GET['phone_number'];
    $row['fax'] = "";
    $row['notes'] = "";
    /* This is a new record */
    $row['new'] = 1;
    
    $sql = "SELECT job_id FROM job_members WHERE user_id = ".sanitize($_SESSION['user_id']);
    //echo $sql;
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    if ($result && mysqli_num_rows($result) > 0) {
        $row_temp = mysqli_fetch_assoc($result);
        $row['job_id'] = $row_temp['job_id'];
    } else {
        $row['job_id'] = "-1";
    }
    //print_pre($_SESSION);
    //print_r($row);exit(0);
    display_script($row);
    display_dispositions();
    display_customer_edit($row);
    
}

require "footer.php";
?>
