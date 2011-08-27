<?php
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
    if (!isset($_GET[phone_number])) {
        redirect("list_customers.php");
        exit(0);
    }
    function display_customer_edit($row) {
        $fields_to_hide[] = "id";
        $fields_to_hide[] = "cleaned_number";
        $fields_to_hide[] = "last_updated";
        $fields_to_hide[] = "status";
        $fields_to_hide[] = "locked_by";
        $fields_to_hide[] = "datetime_locked";
        $fields_to_hide[] = "list_id";
        $fields_to_hide[] = "new";
        $textarea_fields[] = "notes";
        echo '<form action="get_customer.php?save=1" method="post">';
        echo '<table class="sample">';
        foreach ($row as $field=>$value) {
            if (in_array($field, $fields_to_hide)) {
                echo '<input type="hidden" name="'.$field.'" value="'.stripslashes($value).'">';
            } else if (in_array($field, $textarea_fields)) {
                echo '<tr><th colspan="2">'.clean_field_name($field).'</th></tr>';
                echo '<tr><td colspan="2"><textarea cols="60" rows="10" name="'.$field.'">'.stripslashes($value).'</textarea></td></tr>';
            } else {
                echo '<tr><th>'.clean_field_name($field).'</th><td><input type="text" name="'.$field.'" style="width: 400px" value="'.stripslashes($value).'"></td></tr>';
            }
        }
        echo '<tr><td colspan="2"><input type="submit" value="save changes"></td></tr>';
        echo '</form>';
        echo "</table>";
    }
    
    
    /* Disposition */
    echo '<div class="thin_700px_box">';
    echo "Disposition:";
    echo '</div>';
    
    
    
    
    $phone_number = clean_number($_GET[phone_number]);
    $result = mysqli_query($connection, "SELECT * FROM SmoothOperator.customers WHERE cleaned_number = '$phone_number'");
    if (mysqli_num_rows($result) > 0) {
        if (mysqli_num_rows($result) == 1) {
            // Single Row Found
            $row = mysqli_fetch_assoc($result);
            if (isset($_GET['pop'])) {
                $result = mysqli_query($connection, "INSERT INTO interractions (contact_date_time, notes, customer_id) VALUES (NOW(), 'Number screen popped to ".$_SESSION['user_name']." on extension: ".$_SESSION['extension']."', ".$row['id'].")");
                $_SESSION['calls']++;
            } else {
                $result = mysqli_query($connection, "INSERT INTO interractions (contact_date_time, notes, customer_id) VALUES (NOW(), 'Opened by ".$_SESSION['user_name']." on extension: ".$_SESSION['extension']."', ".$row['id'].")");
                
            }
            echo '<div class="thin_700px_box">';
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
        ?>
<div class="messages">
This is a new entry
</div>

<?
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
$row['fax'] = "";
$row['notes'] = "";
/* This is a new record */
$row['new'] = 1;
echo '<div class="thin_700px_box">';
display_customer_edit($row);

}
echo "</div>";
require "footer.php";
?>
