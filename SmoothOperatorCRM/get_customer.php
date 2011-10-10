<?
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
function display_customer_edit($row) {
    global $connection;    
    //print_pre($row);
    if ($row['new'] == 1) {
        ?>
        <script>
        function save_disposition(disposition){
            new Ajax.Request('get_customer.php?save_record=1',{parameters: {phonenumber: <?=$_GET['phone_number']?>}, onSuccess: function(transport){
                             if (transport.responseText) {
                             var response = transport.responseText;
                             var newID = parseInt(response);
                             //alert(response);
                             new Ajax.Request('get_customer.php?save_disposition=1',{parameters: {id: newID, disposition: disposition, user_name: "<?=$_SESSION['user_name']?>", extension: "<?=$_SESSION['extension']?>"}, onSuccess: function(transport){
                                              if (transport.responseText) {
                                              var response = transport.responseText;
                                              //entries_to_ids[counter] = parseInt(response);
                                              //alert('x');
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
        }
        </script>
        <?
    }
    /* Disposition */
    
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
                $close = true;
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
}




/*
 echo "<table>";
 echo "Disposition:<tr>";
 echo "<td><a href=\"aaa\">";
 box_start(100);
 echo "<center>";
 echo "Success<br />";
 ?>
 <br />
 <img src="images/icons/32x32/actions/button_ok.png" alt="Tick">
 <br />
 <br />
 <?
 box_end();
 echo "</a></td>";
 echo "<td><a href=\"aaa\">";
 box_start(100);
 echo "<center>";
 echo "Unavailable<br />";
 ?>
 <br />
 <img src="images/icons/32x32/actions/history.png" alt="Unavailable">
 <br />
 <br />
 <?
 box_end();
 echo "</a></td>";
 echo "<td><a href=\"aaa\">";
 box_start(100);
 echo "<center>";
 echo "Bla<br />";
 ?>
 <br />
 <img src="images/icons/32x32/actions/button_cancel.png" alt="DNC">
 <br />
 <br />
 <?
 box_end();
 echo "</a></td></tr></table>";
 
 */


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
        display_customer_edit($row);
        
        $result = mysqli_query($connection, "SELECT * FROM interractions WHERE customer_id = ".$row['id']." ORDER BY contact_date_time desc");
        if (mysqli_num_rows($result) > 0) {
            echo '<br /><table class="sample">';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr><th>Date: </th><td><b>'.$row['contact_date_time'].'</b></td></tr>';
                echo '<tr><th>Notes: </th><td>'.$row['notes'].'</td></tr><tr><th colspan="2"></th></tr>';
            }
            echo '</table>';
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
    display_customer_edit($row);
    
}
echo "</div>";
require "footer.php";
?>
