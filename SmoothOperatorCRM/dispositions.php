<?
require "header.php";
if (!isset($_GET['search'])) {
    ?>
    <script>
    jQuery(function() {
           jQuery( "#from_date" ).datepicker({
                                             dateFormat : 'yy-mm-dd'
                                             });
           jQuery( "#to_date" ).datepicker({
                                           dateFormat : 'yy-mm-dd'
                                           });
           
           });
    </script>
    <br />
    <?
    box_start();
    ?>
    <center>
    <h3>Search for dispositions</h3>
        <form action = "dispositions.php?search=1" method="post">
        <p>From Date:
        <input type="text" id="from_date" name="from_date" style="width: 200px">
        <br />
        To Date:
        <input type="text" id="to_date"  name="to_date" style="width: 200px">
        <br />
        <?
        $result = mysqli_query($connection, "SELECT * FROM SmoothOperator.jobs");
    if (mysqli_num_rows($result) > 0) {
        echo 'Job: <br /><select name="job">';
        while ($row = mysqli_fetch_assoc($result)) {
            print_pre($row);
            echo '<option value="'.id.'">'.$row['name'].'</option>';
        }
        echo '</select><br /><br />';
    } else {
        ?>
        echo "<h1>You need to create some jobs first!</h1>"
        <?
    }
    ?>
    <input type="submit" value="Display Call Dispositions">
    </p>
    </form>
    <?
    box_end();
} else {
    print_pre($_POST);
//    $result = mysqli
}
require "footer.php";
?>