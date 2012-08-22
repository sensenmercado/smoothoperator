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
    <h3>Search for script_results</h3>
        <form action = "script_results.php?search=1" method="post">
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
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
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
    if (isset($_GET['job'])) {
        $_POST['job'] = $_GET['job'];
    }
    if (isset($_GET['from_date'])) {
        $_POST['from_date'] = $_GET['from_date'];
    }
    if (isset($_GET['to_date'])) {
        $_POST['to_date'] = $_GET['to_date'];
    }
    if (!isset($_GET['download'])) {
        ?>
        <br />
        <a href="script_results.php?search=1&job=<?=$_POST['job']?>&from_date=<?=$_POST['from_date']?>&to_date=<?=$_POST['to_date']?>&download=1">Download Full List</a><br /><br />
        <?
    }
    $sql = "SELECT script_results.*, customers.*, users.username, users.id, script_entries.statement, script_entries.type FROM script_results, customers, users, script_entries where date(question_datetime) between ".sanitize($_POST['from_date'])." and ".sanitize($_POST['to_date'])." and script_results.job_id = ".sanitize($_POST['job'])." and script_results.customer_id = customers.id and script_results.user_id = users.id and script_entries.script_id = script_results.script_id and script_entries.order = script_results.question_number";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    if (mysqli_num_rows($result) == 0) {
        echo "There are no script results!";
    } else {
        ?>
        <div class="thin_90perc_box">
        <table class="sample2" width="100%">
        <?
        echo '<tr>';
        echo '<th>Contact Date/Time</th>';
        echo '<th>Phone Number</th>';
        echo '<th>Question</th>';
        echo '<th>Answer</th>';
        echo '<th>Username</th>';
        echo '<th>Customer Name</th>';
        echo '</tr>';

        
        while ($row = mysqli_fetch_assoc($result)) {
            //print_pre($row);
            echo '<tr>';
            echo '<td>'.$row['question_datetime'].'</td>';
            echo '<td>'.$row['cleaned_number'].'</td>';
            echo '<td>'.$row['statement'].'</td>';
            echo '<td>'.$row['answer'].'</td>';
            echo '<td>'.$row['username'].'</td>';
            echo '<td>'.$row['first_name']." ".$row['last_name'].'</td>';
            echo '</tr>';
        }
        echo '</table></div>';
    }
}
?>

<?
require "footer.php";
?>