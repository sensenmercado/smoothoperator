<?
$rounded[] = 'div.thin_700px_box';
require "header.php";
if (isset($_GET['save'])) {
    $sql = "INSERT INTO reports (name, description) VALUES (".sanitize($_POST['name']).", ".sanitize($_POST['description']).")";
    mysqli_query($connection, $sql);
    redirect("reports.php",0);
    require "footer.php";
    exit(0);
}
if (isset($_GET['add'])) {
    ?>
    <br />
    <form action="reports.php?save=1" method="post">
    <table class="sample">
    <tr>
    <th>Report Name</th>
    <td><input type="text" name="name"></td>
    </tr>
    <tr>
    <th colspan="2">Report Description</th></tr>
    <tr>
    <td colspan="2"><textarea name="description"></textarea></td>
    </tr>
    <tr>
    <td colspan="2"><input type="submit" value="Add Report"></td>
    </tr>
    </table>
    </form>
    <?
    require "footer.php";
    exit(0);
}
if (!isset($_GET['report_id'])) {
    ?><div class='thin_700px_box'><?
    $result = mysqli_query($connection, "SELECT * FROM reports");
    if (mysqli_num_rows($result) == 0) {
        echo 'There are currently no jobs defined - please <a href="reports.php?add=1">add one by clicking here.</a>';
    } else {
        echo '<table class="sample2" width="100%">';
        ?>
        <tr><th>Name</th><th>Description</th><th>Delete</th></tr>
        <?
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td><a href="reports.php?edit='.$row['id'].'"><img src="images/pencil.png">&nbsp;'.$row['name'].'</a></td>';
            echo '<td>'.substr($row['description'],0,30).'</td>';
            echo '<td><a href="reports.php?delete='.$row['id'].'"><img src="images/delete.png">&nbsp;Delete</a></td>';
            //print_pre($row);
            echo '</tr>';
        }
        echo '</table>';
    }
    echo '</div>';
}
?>
<?
require "footer.php";
?>