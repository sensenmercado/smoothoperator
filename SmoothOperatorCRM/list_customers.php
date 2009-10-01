<?
require "header.php";
$result = mysqli_query($connection, "SELECT * FROM SmoothOperator.customers") or die(mysql_error());
$printed_header = false;
echo "<table border=\"1\" class=\"sample\">";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    if (!$printed_header) {
        $printed_header = true;
        foreach ($row as $field=>$value) {
            echo "<th>".ucwords(strtolower(str_replace("_", " ",$field)))."</th>";
        }
        echo "</tr><tr>";
        foreach ($row as $field=>$value) {
            if ($field == "phone") {
                echo '<td><a href="get_customer.php?phone_number='.$value.'">'.$value.'</a></td>';
            } else {
                echo "<td>".$value."</td>";
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
require "footer.php";
?>
