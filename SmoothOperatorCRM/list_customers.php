<?
require "header.php";
?>
<div style="background: #fff;width: 500px;border: 1px solid;margin-top: 30px;padding:40px;">
<?
$result = mysqli_query($connection, "SELECT phone, first_name, last_name FROM SmoothOperator.customers") or die(mysql_error());
$printed_header = false;
echo "<table border=\"1\" class=\"sample\" width=\"100%\">";
$printable[] = "phone";
$printable[] = "first_name";
$printable[] = "last_name";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    if (!$printed_header) {
        $printed_header = true;
        foreach ($row as $field=>$value) {
            if (in_array($field, $printable)) {
                echo "<th>".ucwords(strtolower(str_replace("_", " ",$field)))."</th>";
            }
        }
        echo "</tr><tr>";
        foreach ($row as $field=>$value) {
            if (in_array($field, $printable)) {
                if ($field == "phone") {
                    echo '<td><a href="get_customer.php?phone_number='.$value.'">'.$value.'</a></td>';
                } else {
                    echo "<td>".$value."</td>";
                }
            }
        }
    } else {
        foreach ($row as $field=>$value) {
            echo "<td>".$value."</td>";
        }
    }
    //print_pre($row);
    echo "</tr>";
}
echo "</table>";
?>
</div>
<?
require "footer.php";
?>
