<?
$rounded[] = "div.box_med";
require "header.php";
?>


<div class="box_med" style="width: 400px">

<?
$result = mysqli_query($connection, "SELECT * FROM SmoothOperator.customers") or die(mysql_error());
$printed_header = false;
echo "<table border=\"1\" class=\"sample\" width=\"100%\">";
$printable[] = "phone";
$printable[] = "first_name";
$printable[] = "last_name";
$printable[] = "last_updated";
while ($row = mysqli_fetch_assoc($result)) {
    /* If we haven't printed the table header, do that first */
    if (!$printed_header) {
        $printed_header = true;
        echo "<tr>";
        foreach ($row as $field=>$value) {
            if (in_array($field, $printable)) {
                echo "<th>".ucwords(strtolower(str_replace("_", " ",$field)))."</th>";
            }
        }
        echo "</tr>";
    }

    echo "<tr>";
    foreach ($row as $field=>$value) {
        if (in_array($field, $printable)) {
            if ($field == "phone") {
                echo '<td><a href="get_customer.php?phone_number='.$value.'">'.$value.'</a></td>';
            } else {
                echo "<td>".$value."</td>";
            }
        }
    }
    echo "</tr>";
}
echo "</table>";
?>
</div>
<?
require "footer.php";
?>
