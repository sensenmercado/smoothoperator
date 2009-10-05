<?php
require "header.php";

//box_start();
?>
<a href="get_customer.php?call=<?=$_GET['phone_number']?>">
<img src="images/icons/32x32/apps/chat.png" />
<font size="5">
Call
</font></a>&nbsp;

<a href="get_customer.php?next=1">
<img src="images/icons/32x32/apps/chat.png" />
<font size="5">
Next Number
</font></a>&nbsp;

<a href="get_customer.php?hangup=1">
<img src="images/icons/32x32/apps/chat.png" />
<font size="5">
Hangup
</font></a>&nbsp;

<?
//echo "Call Now";
//box_end();

if (!isset($_GET[phone_number])) {
    redirect("list_customers.php");
    exit(0);
}

function display_customer_edit($row) {
    $fields_to_hide[] = "id";
    $fields_to_hide[] = "cleaned_number";
    $fields_to_hide[] = "last_updated";
    $textarea_fields[] = "notes";
    echo '<form action="get_customer.php" method="post">';
    echo '<table class="sample">';
    foreach ($row as $field=>$value) {
        if (in_array($field, $fields_to_hide)) {
            echo '<input type="hidden" name="'.$field.'" value="'.$value.'">';
        } else if (in_array($field, $textarea_fields)) {
            echo '<tr><th colspan="2">'.clean_field_name($field).'</th></tr>';
            echo '<tr><td colspan="2"><textarea cols="60" rows="10" name="'.$field.'">'.$value.'</textarea></td></tr>';
        } else {
            echo '<tr><th>'.clean_field_name($field).'</th><td><input type="text" name="'.$field.'" style="width: 400px" value="'.$value.'"></td></tr>';
        }
    }
    echo '<tr><td colspan="2"><input type="submit" value="save changes"></td></tr>';
    echo '</form>';
    echo "</table>";
}

$phone_number = clean_number($_GET[phone_number]);
$result = mysqli_query($connection, "SELECT * FROM SmoothOperator.customers WHERE cleaned_number = '$phone_number'");
if (mysqli_num_rows($result) > 0) {
    if (mysqli_num_rows($result) == 1) {
        // Single Row Found
        $row = mysqli_fetch_assoc($result);
        display_customer_edit($row);
    } else {
        // Multiple Rows Found
    }
} else {
    echo "Not Found";
}
require "footer.php";
?>