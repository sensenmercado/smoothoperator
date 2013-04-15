<?
if ((!isset($_GET['download'])) && (!isset($_GET['get_dispositions']))) {
    require "header.php";
} else {
    require "config/db_config.php";
    require "functions/sanitize.php";
}
if (isset($_GET['get_dispositions'])) {
    //    $array = array(1,2,3,4,5,6);
    $result = mysqli_query($connection, "SELECT id, text FROM job_dispositions where job_id = ".sanitize($_GET['get_dispositions']));
    while ($row = mysqli_fetch_assoc($result)) {
        $array[$row['id']] = ucwords($row['text']);
    }
    echo json_encode($array);
    exit(0);
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
        echo 'Job: <br /><select name="job" onchange="update_dispositions(jQuery(this).val())">';
        echo '<option value="-1" >-- Please Select a Job --</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            print_pre($row);
            echo '<option value="'.$row['id'].'" >'.$row['name'].'</option>';
        }
        echo '</select><br /><br />';
    } else {
        ?>
        echo "<h1>You need to create some jobs first!</h1>"
        <?
    }
    ?>
    <div id="content1">
    </div>
    <br />
    <script>
    function update_dispositions(id) {
        jQuery("#content1").html("");
        jQuery.getJSON('script_results.php?get_dispositions='+id, function(data) {
                       var items = [];
                       jQuery.each(data, function(key, val) {
//                                   <input type="checkbox" name="formDoor[]" value="D" />Drake Commons
                                   jQuery("#content1").append('<input type="checkbox" name="dispositions[]" value="' + key + '"> ' + val +'<br />');
                                   });
                       });
    }
    </script>
    
    
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
    if (isset($_GET['dispositions[]'])) {
        $_POST['dispositions[]'] = $_GET['dispositions[]'];
    }
    $dispositions = "";
    foreach ($_REQUEST['dispositions'] as $disposition) {
        $dispositions.=$disposition.",";
    }
    $dispositions = substr($dispositions,0,-1);
    //echo $dispositions;
    $stuff['dispositions']= $_POST['dispositions'];
    $disps= http_build_query($stuff);
    //exit(0);
    if (!isset($_GET['download'])) {
        ?>
        <br />
        <a href="script_results.php?search=1&job=<?=$_POST['job']?>&from_date=<?=$_POST['from_date']?>&to_date=<?=$_POST['to_date']?>&download=1&<?=$disps?>">Download Full List</a><br /><br />
        <?
    }
    if (0&&isset($_GET['show_lead'])) {
//        $sql = "SELECT script_entries.statement, script_results.*, customers.*, users.username, users.id,  customers.id as customer_id, script_entries.type FROM script_entries, script_results, customers, users where date(question_datetime) between ".sanitize($_POST['from_date'])." and ".sanitize($_POST['to_date'])." and script_results.job_id = ".sanitize($_POST['job'])." and script_results.customer_id = customers.id and script_results.user_id = users.id and script_entries.script_id = script_results.script_id and script_entries.order = script_results.question_number and customers.id = ".sanitize($_GET['customer_id']);
        $sql = "SELECT script_entries.statement, script_results.*, job_dispositions.text as disposition_text, customers.*, users.username, users.id,  customers.id as customer_id, script_entries.type FROM script_entries, script_results, customers, users, customer_dispositions, job_dispositions where date(question_datetime) between ".sanitize($_POST['from_date'])." and ".sanitize($_POST['to_date'])." and script_results.job_id = ".sanitize($_POST['job'])." and script_results.customer_id = customers.id and script_results.user_id = users.id and script_entries.script_id = script_results.script_id and script_entries.order = script_results.question_number and customer_dispositions.`customer_id`=customers.id and job_dispositions.id = disposition and job_dispositions.id in (".$dispositions.")  and customers.id = ".sanitize($_GET['customer_id'];
    } else {
        $sql = "SELECT script_entries.statement, script_results.*, job_dispositions.text as disposition_text, customers.*, users.username, users.id,  customers.id as customer_id, script_entries.type FROM script_entries, script_results, customers, users, customer_dispositions, job_dispositions where date(question_datetime) between ".sanitize($_POST['from_date'])." and ".sanitize($_POST['to_date'])." and script_results.job_id = ".sanitize($_POST['job'])." and script_results.customer_id = customers.id and script_results.user_id = users.id and script_entries.script_id = script_results.script_id and script_entries.order = script_results.question_number and customer_dispositions.`customer_id`=customers.id and job_dispositions.id = disposition and job_dispositions.id in (".$dispositions.")";
    }
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    //echo $sql;
    $dont_display[] = "customer_id";
    $dont_display[] = "script_id";
    $dont_display[] = "question_number";
    $dont_display[] = "last_updated";
    $dont_display[] = "job_id";
    $dont_display[] = "user_id";
    $dont_display[] = "id";
    $dont_display[] = "list_id";
    $dont_display[] = "status";
    $dont_display[] = "locked_by";
    $dont_display[] = "datetime_locked";
    $dont_display[] = "do_not_call";
    $dont_display[] = "do_not_call_reason";
    $dont_display[] = "username";
    $dont_display[] = "type";
    
    if (mysqli_num_rows($result) == 0) {
        echo "There are no script results!";
    } else {
        if (isset($_GET['download'])) {
            //echo "<pre>";
            $row_new = array();
            while ($row = mysqli_fetch_assoc($result)) {
                //print_r($row);
                $row['statement_'.$row['question_number']] = $row['statement'];
                $row['answer_'.$row['question_number']] = $row['answer'];
                unset($row['statement']);
                unset($row['answer']);
                foreach ($row as $field=>$value) {
                    $remove = false;
                    foreach ($dont_display as $field_to_remove) {
                        if ($field == $field_to_remove) {
                            $remove = true;
                        }
                    }
                    if (!$remove) {
                        $row_new[$row['customer_id']][$field] = $value;
                    }
                }
                
            }
            //print_r($row_new);
            //exit(0);
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=Full_Dispositions_Job_".$_POST['job']."_".$_POST['from_date']."-".$_POST['to_date'].".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $header_printed = false;
            foreach ($row_new as $row) {
                if ($header_printed == false) {
                    $header_printed = true;
                    foreach ($row as $field=>$value) {
                        echo str_replace(",","",$field).",";
                    }
                    echo "Unused\n";
                }
                $row['disposition'] = $disps[$row['disposition']];
                foreach ($row as $field=>$value) {
                    echo sanitize(str_replace(",","",$value), false).",";
                }
                echo "''\n";
            }
            exit(0);
        } else if (isset($_GET['show_lead'])) {
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
                echo '<td>'.format_phone_number($row['cleaned_number']).'</td>';
                echo '<td>'.$row['statement'].'</td>';
                echo '<td>'.$row['answer'].'</td>';
                echo '<td>'.$row['username'].'</td>';
                echo '<td>'.$row['first_name']." ".$row['last_name'].'</td>';
                echo '</tr>';
            }
            echo '</table></div>';
        } else {
            ?><div class="thin_90perc_box">
            <table class="sample2" width="100%">
            <?
            echo '<tr>';
            echo '<th>Contact Date/Time</th>';
            echo '<th>Phone Number</th>';
            echo '<th>Username</th>';
            echo '<th>Customer Name</th>';
            echo '</tr>';
            $number = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['cleaned_number'] != $number) {
                    $number = $row['cleaned_number'];
                    echo '<tr>';
                    echo '<td>'.$row['question_datetime'].'</td>';
                    echo '<td><a href="script_results.php?show_lead=1&job='.$_POST['job'].'&search=1&from_date='.$_POST['from_date'].'&to_date='.$_POST['to_date'].'&customer_id='.$row['customer_id'].'&'.$disps.'"><img src="images/magnifier.png">&nbsp;'.format_phone_number($row['cleaned_number']).'</a></td>';
                    echo '<td>'.$row['username'].'</td>';
                    echo '<td>'.$row['first_name']." ".$row['last_name'].'</td>';
                    echo '</tr>';
                }
            }
            echo '</table></div>';
        }
    }
}
?>

<?
require "footer.php";
?>