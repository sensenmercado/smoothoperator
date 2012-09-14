<?
function maybeEncodeCSVField($string) {
    if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
        $string = '"' . str_replace('"', '""', $string) . '"';
    }
    return $string;
}
if (!isset($_GET['download'])) {
    require "header.php";
} else {
    require "config/db_config.php";
    require "functions/sanitize.php";
}
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
        <a href="dispositions.php?search=1&job=<?=$_POST['job']?>&from_date=<?=$_POST['from_date']?>&to_date=<?=$_POST['to_date']?>&download=1">Download Full List</a><br /><br />
        <?
    }
    $sql = "SELECT customer_dispositions.*, customers.*, users.username, users.id FROM customer_dispositions, customers, users where date(contact_date_time) between ".sanitize($_POST['from_date'])." and ".sanitize($_POST['to_date'])." and customer_dispositions.job_id = ".sanitize($_POST['job'])." and customer_dispositions.customer_id = customers.id and customer_dispositions.user_id = users.id";
    //    echo $sql;
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    $result_dispositions = mysqli_query($connection, "SELECT * FROM job_dispositions");
    $disps = array();
    while ($row_disp = mysqli_fetch_assoc($result_dispositions)) {
        $disps[$row_disp['id']] = $row_disp['text'];
    }
    if (mysqli_num_rows($result) == 0) {
        echo "No data found for that job/time period";
    } else {
        if (isset($_GET['download'])) {
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Dispositions_Job_".$_POST['job']."_".$_POST['from_date']."-".$_POST['to_date'].".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $header_printed = false;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($header_printed == false) {
                    $header_printed = true;
                    foreach ($row as $field=>$value) {
                        echo $field.",";
                    }
                    echo "Unused\n";
                }
                $row['disposition'] = $disps[$row['disposition']];
                foreach ($row as $field=>$value) {
                    echo sanitize($value, false).",";
                }
                echo "''\n";
            }
        } else {
            ?>
            <div class="thin_90perc_box">
            <table class="sample2" width="100%">
            <?
            echo '<tr>';
            echo '<th>Contact Date/Time</th>';
            echo '<th>Phone Number</th>';
            echo '<th>Disposition</th>';
            echo '<th>Username</th>';
            echo '<th>Customer Name</th>';
            echo '</tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                //print_pre($row);
                echo '<tr>';
                echo '<td>'.$row['contact_date_time'].'</td>';
                echo '<td>'.$row['cleaned_number'].'</td>';
                echo '<td>'.ucwords(strtolower($disps[$row['disposition']])).'</td>';
                echo '<td>'.$row['username'].'</td>';
                echo '<td>'.$row['first_name']." ".$row['last_name'].'</td>';
                echo '</tr>';
            }
            echo '</table></div>';
        }
    }
}
if (!isset($_GET['download'])) {
    require "footer.php";
}
?>