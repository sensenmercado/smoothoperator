<?php
require "header.php";
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
    echo "<table>";
    foreach ($row as $field=>$value) {
        if (in_array($field, $fields_to_hide)) {
            echo '<input type="hidden" name="'.$field.'" value="'.$value.'">';
        } else if (in_array($field, $textarea_fields)) {
            echo '<tr><td colspan="2">'.clean_field_name($field).'</td></tr>';
            echo '<tr><td colspan="2"><textarea cols="60" rows="10" name="'.$field.'">'.$value.'</textarea></td></tr>';
        } else {
            echo '<tr><td>'.clean_field_name($field).'</td><td><input type="text" name="'.$field.'" value="'.$value.'"></td></tr>';
        }
    }
    echo '<tr><td colspan="2"><input type="submit" value="save changes"></td></tr>';
    echo '</form>';
    echo "</table>";
}

$phone_number = clean_number($_GET[phone_number]);
$result = mysql_query("SELECT * FROM SmoothOperator.customers WHERE cleaned_number = '$phone_number'");
if (mysql_num_rows($result) > 0) {
    if (mysql_num_rows($result) == 1) {
        // Single Row Found
        $row = mysql_fetch_assoc($result);
        display_customer_edit($row);
    } else {
        // Multiple Rows Found
    }
} else {
    echo "Not Found";
}
require "footer.php";
?>
