<?
if (isset($_GET['reschedule_number'])) {
    require "header.php";
    $sql = "INSERT INTO reschedule (phone_number, reschedule_datetime, user) VALUES (".sanitize($_GET['phone_number']).",".sanitize($_GET['date']." ".$_GET['time']).", ".$_SESSION['user_id'].")";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    redirect("get_customer.php?from=".$_GET['from']."&phone_number=".$_GET['phone_number'],3,"Rescheduling a call for ".$_GET['time']." on ".$_GET['date']);
    require "footer.php";
    exit(0);
}
if (isset($_GET['appointment'])) {
    require "header.php";
    $sql = "INSERT INTO appointments (customer_id, reschedule_datetime, user) VALUES (".sanitize($_GET['customer_id']).",".sanitize($_GET['date']." ".$_GET['time']).", ".$_SESSION['user_id'].")";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    redirect("get_customer.php?from=".$_GET['from']."&phone_number=".$_GET['phone_number'],3,"Setting appointment for ".$_GET['time']." on ".$_GET['date']);
    require "footer.php";
    exit(0);
}
if (isset($_GET['save_script'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    session_start();
    /*    $sql = "INSERT INTO customers (`phone`, `cleaned_number`) VALUES (".sanitize($_POST['phonenumber']).",".sanitize(clean_number($_POST['phonenumber'])).")";
     $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
     $new_id = mysqli_insert_id($connection);
     echo $new_id;
     */
    print_r($_GET);
    foreach ($_GET as $field=>$value) {
        if (strlen($field) > 5 && substr($field, 0, 5) == "field") {
            $fieldnum = substr($field,5);
            echo $fieldnum.":".$value."\n";
            $sql = "REPLACE INTO script_results (customer_id, script_id, question_number, answer, job_id, user_id) VALUES (".sanitize($_GET['customer_id']).",".sanitize($_GET['script_id']).",".sanitize($fieldnum).",".sanitize($value).",".sanitize($_SESSION['job_id']).",".$_SESSION['user_id'].")";
            echo $sql;
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
    session_start();
    require "config/db_config.php";
    require "functions/sanitize.php";
    $sql = "INSERT INTO SmoothOperator.customer_dispositions (contact_date_time, disposition, user_id, extension, customer_id, job_id) VALUES (NOW(), ".sanitize($_POST['disposition']).", ".sanitize($_POST['user_id']).", ".sanitize($_POST['extension']).", ".sanitize($_POST['id']).", ".sanitize($_SESSION['job_id']).")";
    //    echo $sql;
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
        if (isset($_GET['nomenu'])) {
            echo '<script>window.close();</script>';
        } else {
            redirect("list_customers.php");
        }
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
        if (isset($_GET['nomenu'])) {
            echo '<script>window.close();</script>';
        } else {
            redirect("list_customers.php");
        }
        
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
function display_script($customer, $question_number) {
    global $connection;
    ?>
    <script>
    
    
    var entries_to_ids=new Array();
    var counter = 0;
    function addInput(value,new_id,divName){
        jQuery("#"+divName).append("Entry <br><input type='text' name='myInputs[]'>");
    }
    function add_end_of_section(value,new_id,divName){
        jQuery("#"+divName).append("<hr />");
    }
    
    /* Statement followed by text field */
    
    function add_statement_followed_by_text_field(value,new_id,statement, divName){
        jQuery("#"+divName).append(nl2br(statement)+" <br><input type='text' name='field"+new_id+"' value='"+value+"'>");
    }
    
    /* Add priority */
    
    function add_priority(value,new_id,divName){
        jQuery("#"+divName).append(" <br>Lead Priority: <select name='field"+new_id+"'><option value='0'>Normal</option><option value='0'>High</option><option value='0'>Critical</option></select><br />");
    }
    
    /* Statement followed by yes/no */
    
    function add_statement_followed_by_yesno(value,new_id,statement, divName){
        if (value == "YES") {
            jQuery("#"+divName).append(nl2br(statement)+" <br><select name='field"+new_id+"'><option value='YES' selected>Yes</option><option value='NO'>No</option></select><br />");
            
        } else {
            jQuery("#"+divName).append(nl2br(statement)+" <br><select name='field"+new_id+"'><option value='YES'>Yes</option><option value='NO' selected>No</option></select><br />");
        }
    }
    
    function add_statement_followed_by_combobox_from_db(value,new_id,statement, comboboxes, divName){
        var ih = nl2br(statement)+" <br><select name='field"+new_id+"'>";
        comboboxes.forEach(
                           function(item){
                           if (item == value) {
                           ih += "<option value='"+item+"' selected>"+item+"</option>";
                           } else {
                            ih += "<option value='"+item+"'>"+item+"</option>";
                           }
                           }
                           );
        ih += "</select>";
        jQuery("#"+divName).append(ih+"<br />");
    }
    
    /* Statement followed by nothing */
    
    function add_statement_followed_by_nothing(value,new_id,statement, divName){
        jQuery("#"+divName).append(nl2br(statement)+"");
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
    
    $result_top_question = mysqli_query($connection, "SELECT * FROM script_entries WHERE script_id = ".$script_id." order by `order` desc limit 1") or die(mysqli_error($connection));

    $row_result_top_question = mysqli_fetch_assoc($result_top_question);
    $top_question_number = $row_result_top_question['order'];
    
    
    $result_entries = mysqli_query($connection, "SELECT * FROM script_entries WHERE script_id = ".$script_id." AND `order` = ".$question_number) or die(mysqli_error($connection));
    $x = 0;
    
    $result_previous = mysqli_query($connection, "SELECT * FROM script_results WHERE script_id = ".$script_id." and customer_id = ".$customer['id']." and question_number = ".$question_number) or die(mysqli_error($connection));
    
    if (@mysqli_num_rows($result_previous) == 0) {
        $value = "''";
    } else {
        $row_previous = mysqli_fetch_assoc($result_previous);
        $value = sanitize(str_replace("'","",stripslashes($row_previous['answer'])));
    }
    
    if (mysqli_num_rows($result_entries) == 0) {
        
    } else {
        while ($row_entries = mysqli_fetch_assoc($result_entries)) {
            $row_entries['statement'] = str_replace("{first_name}","<b>".$customer['first_name']."</b>",$row_entries['statement']);
            $row_entries['statement'] = str_replace("{last_name}","<b>".$customer['last_name']."</b>",$row_entries['statement']);
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
                    <script>add_statement_followed_by_text_field(<?=$value?>,<?=$row_entries['order']?>,<?=stripslashes(sanitize($row_entries['statement']))?>, 'dynamicInput2');</script>
                    <?
                    break;
                case 1:
                    ?>
                    <script>add_statement_followed_by_yesno(<?=$value?>,<?=$row_entries['order']?>,<?=stripslashes(sanitize($row_entries['statement']))?>, 'dynamicInput2');</script>
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
                    add_statement_followed_by_combobox_from_db(<?=$value?>,<?=$row_entries['order']?>,<?=stripslashes(sanitize($row_entries['statement']))?>, combobox_entries, 'dynamicInput2');
                    </script>
                    <?
                    break;
                case 3:
                    ?>
                    <script>add_statement_followed_by_nothing(<?=$value?>,<?=$row_entries['order']?>,<?=stripslashes(sanitize($row_entries['statement']))?>, 'dynamicInput2');</script>
                    <?
                    break;
                case 4:
                    ?>
                    <script>add_priority(<?=$value?>,<?=$row_entries['order']?>,'dynamicInput2');</script>
                    <?
                    break;
                case -1:
                    ?>
                    <script>add_end_of_section(<?=$value?>,<?=$row_entries['order']?>,'dynamicInput2');</script>
                    <?
                    break;
            }
            
        }
    }
    /* Clean the query string so that we remove any previous references to question_number */
    foreach ($_GET as $field=>$value) {
        if ($field != "question_number") {
            $query_string.=$field."=".$value."&";
        }
    }
    $query_string1 = $query_string."question_number=".($question_number+1);
    $query_string0 = $query_string."question_number=".($question_number-1);
    
    
    
    
    if ($question_number > 1) {
        ?>
        <script>
        

        jQuery("#dynamicInput2").append('<br /><a class="button_link" href="#" onclick="save_customer_details();new Ajax.Request(\'get_customer.php?save_script=1&customer_id=<?=$customer['id']?>&\'+jQuery(\'#script_form\').serialize(),{onSuccess: function(transport){if (transport.responseText) {jQuery(\'#dynamicInput3\').fadeOut(1000);window.location=\'get_customer.php?<?=$query_string0?>\';}}});"><img src="images/resultset_previous.png">Previous Question</a>&nbsp;');

        <?
        if ($question_number < $top_question_number) {
            ?>
        
        jQuery("#dynamicInput2").append('<a class="button_link" href="#" onclick="save_customer_details();new Ajax.Request(\'get_customer.php?save_script=1&customer_id=<?=$customer['id']?>&\'+jQuery(\'#script_form\').serialize(),{onSuccess: function(transport){if (transport.responseText) {jQuery(\'#dynamicInput3\').fadeOut(1000);window.location=\'get_customer.php?<?=$query_string1?>\';}}});">Previous Question&nbsp;<img src="images/resultset_next.png"></a>');
          
            <?
        } else {
            ?>
            jQuery("#dynamicInput2").append('<a class="button_link" href="#" onclick="save_customer_details();new Ajax.Request(\'get_customer.php?save_script=1&customer_id=<?=$customer['id']?>&\'+jQuery(\'#script_form\').serialize());">Finish&nbsp;<img src="images/control_stop_blue.png"></a>');

            <?
        }
        ?>
        
        </script>
        <?
    } else {
        ?>
        <script>
        
        <?
        if ($question_number < $top_question_number) {
            ?>
        jQuery("#dynamicInput2").append('<a class="button_link" href="#" onclick="new Ajax.Request(\'get_customer.php?save_script=1&customer_id=<?=$customer['id']?>&\'+jQuery(\'#script_form\').serialize(),{onSuccess: function(transport){if (transport.responseText) {jQuery(\'#dynamicInput3\').fadeOut(1000);window.location=\'get_customer.php?<?=$query_string1?>\';}}});">Next Question&nbsp;<img src="images/resultset_next.png"></a>');
            <?
        } else {
        ?>
            jQuery("#dynamicInput2").append('<a class="button_link" href="#" onclick="new Ajax.Request(\'get_customer.php?save_script=1&customer_id=<?=$customer['id']?>&\'+jQuery(\'#script_form\').serialize(),{onSuccess: function(transport){if (transport.responseText) {jQuery(\'#dynamicInput3\').fadeOut(5000);}}});">Next Question&nbsp;<img src="images/control_stop_blue.png"></a>');
            <?
        }
        ?>

        
        
        </script>
        <?
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
                //echo "<table id=\"dispositions\">";
                $closed = false;
                
                // Add disposition for rescheduling a call
                //echo "<tr>";
                $x++;
                //echo "<td>";
                
                echo '<button class="button_reschedule" onclick="reschedule();">';
                
                //box_start(145);
                //echo "<center>";
                /*?>
                <img src = "images/clock.png" alt="Reschedule Call">
                <?*/
                echo "Reschedule Call";
                //box_end();
                echo '</button>';
                //echo "</td>";
                
                
                
                
                while ($rowx = mysqli_fetch_assoc($result_dispositions)) {
                    if ($x == 0) {
                        //echo "<tr>";
                        $closed = false;
                    }
                    $x++;
                    //echo "<td>";
                    
                    /* When you click a dispostion you need to do the following:
                     
                     1. Check the current record is in the database ($row['new'] != 1)
                     2. Add an interraction for the current record
                     3. Remove the dispositions
                     
                     */
                    
                    echo '<button class="buttonx" onclick="save_disposition(\''.$rowx['id'].'\');jQuery(\'#dispositions\').fadeOut(500);jQuery(\'#status_bar\').text(\'Disposition set to '.$rowx['text'].'\');jQuery(\'#status_bar\').fadeIn(2000);jQuery(\'#status_bar\').fadeOut(5000);">';
                    
                    //box_start(135);
                    //echo "<center>";
                    /*?>
                    <img src = "images/database.png" >
                    <?*/
                    echo ucfirst(strtolower($rowx['text']));
                    //box_end();
                    echo '</button>';
                    
                    
                    
                    
                    //echo "</td>";
                    if ($x > 5) {
                        $x = 0;
                        //echo "</tr>";
                        $closed = true;
                    }
                    //echo '</div></div>';
                }
                
                
                if (!$closed) {
                    //echo "</tr>";
                }
                //echo "</table>";
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
    <script>
    function save_customer_details() {
        jQuery.post("get_customer.php?save=1&nomenu=1", jQuery("#customer_form").serialize());
    }

    </script>
    
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
                             
                             new Ajax.Request('get_customer.php?save_disposition=1',{parameters: {id: window.newID, disposition: disposition, user_id: "<?=$_SESSION['user_id']?>", extension: "<?=$_SESSION['extension']?>"}, onSuccess: function(transport){
                                              if (transport.responseText) {
                                              var response = transport.responseText;
                                              //entries_to_ids[counter] = parseInt(response);
                                              //alert(response);
                                              window.location="get_customer.php?phone_number=<?=$_GET['phone_number']?>&disposition_set=1";
                                              }
                                              }
                                              });
                             }
                             
                             }
                             });
            jQuery("#customer_form").submit();
            
            
        }
        
        
        
        
        
        
        </script>
        <?
    } else {
        ?>
        <script>
        
        function save_disposition(disposition){
            new Ajax.Request('get_customer.php?save_disposition=1',{parameters: {id: <?=$row['id']?>, disposition: disposition, user_id: "<?=$_SESSION['user_id']?>", extension: "<?=$_SESSION['extension']?>"}, onSuccess: function(transport){
                             if (transport.responseText) {
                             //alert("x");
                             var response = transport.responseText;
                             //entries_to_ids[counter] = parseInt(response);
                             jQuery('#status_bar').text("Saved Disposition");
                             jQuery('#status_bar').fadeIn(1000);
                             jQuery('#status_bar').fadeOut(1000);
                             //alert(response);
                             }
                             new Ajax.Request('get_customer.php?save_script=1&customer_id='+<?=$row['id']?>+'&'+jQuery("#script_form").serialize(),{
                                              onSuccess: function(transport){
                                              if (transport.responseText) {
                                              var response = transport.responseText;
                                              jQuery('#dynamicInput3').fadeOut(1000);
                                              //alert(response);
                                              }
                                              //alert("x");
                                              jQuery("#customer_form").submit();
                                              
                                              }
                                              
                                              });
                             
                             
                             }
                             });
            //sleep(3)
            //
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
        jQuery("#content").append('<div id="reschedule" style="display: none" title="Reschedule Call"><center><form id="reschedule_form">Date: <input id="date-picker" name="date-picker"><br />Time: <input type="text" id="time-picker" name="time-picker" value="<?=@date("H:i")?>" style="width: 50px"><input id="done" type="submit" value="Reschedule Call"></form></div>');
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
    function appointment(){
        
        <?
        /*foreach ($_GET as $field=>$value) {
         ?>alert('<?=$field."=".$value?>');<?
         }*/
        ?>
        jQuery("#content").append('<div id="appointment" style="display: none" title="Create Appointment"><center><form id="appointment_form">Date: <input id="date-picker" name="date-picker"><br />Time: <input type="text" id="time-picker" name="time-picker" value="<?=@date("H:i")?>" style="width: 50px"><input id="done" type="submit" value="Create Appointment"></form></div>');
        jQuery('#date-picker').datepicker({
                                          dateFormat : 'yy-mm-dd'
                                          });
        jQuery("#appointment").dialog();
        jQuery("#appointment_form").submit(function(e) {
                                           e.preventDefault();
                                           window.location = "get_customer.php?appointment=1&phone_number=<?=$_GET['phone_number']?>&customer_id=<?=$row['id']?>&from=list&date="+jQuery("#date-picker").val()+"&time="+jQuery("#time-picker").val();
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
    if (isset($_GET['nomenu'])) {
        echo '<form id="customer_form" action="get_customer.php?save=1&nomenu=1" method="post">';
    } else {
        echo '<form id="customer_form" action="get_customer.php?save=1" method="post">';
    }
    
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
    echo '<tr><td colspan="2"><input type="hidden" value="save changes"></td></tr>';
    echo '</form>';
    echo "</table>";
    echo '<center><a href="javascript:void(0)" onclick="save_customer_details();appointment();">Create an appointment</a>';
    echo "</div>";
}
?>
<?
if (isset($_GET['nomenu'])) {
    ?>
    <script>
    if (window.opener && !window.opener.closed) {
        window.opener.location.href = "index.php";
    }
    </script>
    <?
}

/* ========================== */
/* Load a phone number record */
/* ========================== */
$phone_number = clean_number($_GET['phone_number']);

/* Get the details of the campaigns that relate to the job the agent is in */
$sql = "SELECT list_id FROM SmoothOperator.campaigns WHERE job_id = ".sanitize($_SESSION['job_id']);
$result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
if (mysqli_num_rows($result) > 0) {
    /* We have found details - extract the list ID they're working on */
    $row = mysqli_fetch_assoc($result);
    $list_id = $row['list_id'];
    $get_number_sql = "SELECT * FROM SmoothOperator.customers WHERE cleaned_number = '$phone_number' and list_id = $list_id order by id desc limit 1";
    $result = mysqli_query($connection, $get_number_sql);
    if (mysqli_num_rows($result) == 0) {
        /* If there is no number in that list, try doing a generic search */
        $get_number_sql = "SELECT * FROM SmoothOperator.customers WHERE cleaned_number = '$phone_number' order by id desc limit 1";
        $result = mysqli_query($connection, $get_number_sql);
    }
} else {
    // This user is either not in a job or the job they are in has not run a campaign
    $get_number_sql = "SELECT * FROM SmoothOperator.customers WHERE cleaned_number = '$phone_number' order by id desc limit 1";
    $result = mysqli_query($connection, $get_number_sql);
}

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
        if (isset($_GET['question_number'])) {
            $question_number = 0+$_GET['question_number'];
        } else {
            $question_number = 1;
        }
        display_script($row, $question_number);
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
    if (isset($_GET['question_number'])) {
        $question_number = 0+$_GET['question_number'];
    } else {
        $question_number = 1;
    }
    display_script($row, $question_number);
    display_dispositions();
    display_customer_edit($row);
}

require "footer.php";
?>
