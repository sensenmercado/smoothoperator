<?
$rounded[] = "div.thin_700px_box";
require "header.php";
?>


<div class="thin_700px_box" >

<?
$result = mysqli_query($connection, "SELECT * FROM SmoothOperator.customers limit 100") or die(mysql_error());
$printed_header = false;
echo "<table border=\"1\" class=\"sample2\" width=\"100%\" style=\"background: #eee\">";
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

    echo "<tr onmouseover=\"this.style.background='#bcf ';this.style.cursor='pointer'\" onmouseout=\"this.style.background='#eee';\" onclick=\"window.location.href='get_customer.php?from=list&phone_number=".$row['phone']."';\">";
    foreach ($row as $field=>$value) {
        if (in_array($field, $printable)) {
            echo "<td>".$value."</td>";
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
