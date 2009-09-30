<?
require "header.php";
if (isset($_GET[save])) {
    print_pre($_POST);
    draw_progress("Please wait we are saving your changes...");
    redirect("users.php", 10);
    exit(0);
}
if (isset($_GET[edit])) {
    function display_user_edit($row) {
        $fields_to_hide[] = "id";
        $fields_to_hide[] = "password";
        $textarea_fields[] = "";
        echo '<form action="users.php?save=1" method="post">';
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
    $id = sanitize($_GET[edit]);
    $result = mysql_query("SELECT * FROM users WHERE id = $id LIMIT 1");
    if (mysql_num_rows($result) > 0) {
        $row = mysql_fetch_assoc($result);
        //print_pre($row);
        display_user_edit($row);
    }
    require "footer.php";
    exit(0);
}
?>
<table class="sample">
    <tr>
        <th>Username</th>
        <th>Name</th>
        <th>Security Level</th>
        <th>Delete</th>
    </tr>
    <?
    $result = @mysql_query("SELECT * FROM users");
    while ($row = mysql_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td><a href="users.php?edit='.$row[id].'">'.$row[username].'&nbsp;<img src="images/pencil.png"></a></td>';
        echo "<td>$row[first_name] $row[last_name]</td>";
        echo "<td>$row[security_level]</td>";
        echo '<td><a href="users.php?delete='.$row[id].'"><img src="images/delete.png"></td>';
        echo '</tr>';
    }
    ?>
</table>
<?
require "footer.php";
?>
