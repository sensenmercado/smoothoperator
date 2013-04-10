<?
/* The idea here is to go through and find any calls that were answered but
 * less than 5 seconds and set them to a disposition of hung up if they haven't
 * already been done.  There are a couple of complications with this seemingly
 * simple idea. The software has no idea what a "hung up" disposition is. They
 * are abstract text representations. The next problem is that each campaign has
 * it's own hung up dispositions. The only real way to do this is to go through
 * each disposition for the campaign and see which one matches the text "hung up"
 * most closely and then choose that as the entry. Sucks and is somewhat 
 * horrible but what's a guy to do.
 */

require "config/db_config.php";

$string_to_match = "hung up";
$result = mysqli_query($connection,"SELECT * FROM job_dispositions order by job_id");
$job_id = 0;
$best_match_number = 0;
$best_match = "";
$best_match_id = 0;
while ($row = mysqli_fetch_assoc($result)) {
    //print_r($row);
    if ($row['job_id'] != $job_id) {
        if ($job_id != 0) {
            echo "<h3>Best Match for ID: $job_id is $best_match ($best_match_id) with a match of $best_match_number</h3>\n";
            $hung_up[$job_id] = $best_match_id;
        }
        $job_id = $row['job_id'];
        $best_match_number = 0;
        $best_match = "";
        $best_match_id = 0;
    }
    similar_text(strtolower($row['text']),$string_to_match,$p);
    if ($p > $best_match_number) {
        $best_match_number = $p;
        $best_match = $row['text'];
        $best_match_id = $row['id'];
    }
    echo "<b>".$row['text']."</b> ".$p."\n";
}
echo "<h3>Best Match for ID: $job_id is $best_match ($best_match_id) with a match of $best_match_number</h3>\n";
$hung_up[$job_id] = $best_match_id;

// Now we have the matches (or closest) so we can start finding records
$sql = 'select userfield, uniqueid, billsec, calldate from cdr where userfield is not null and userfield != "" and userfield2 != "adjusted" and billsec < 50 and disposition = "ANSWERED"';
$result = mysqli_query($connection, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $split = explode("-",$row['userfield']);
    $phonenumber = $split[0];
    $job = $split[1]-100000;
    $uniqueid = $row['uniqueid'];
    echo "Job: $job Phone Number: $phonenumber UniqueID: $uniqueid\n";
    $jobs[$phonenumber] = $job;
    $sql = "select id from customers where cleaned_number = ".$phonenumber." order by last_updated desc limit 1";
    $result_customer = mysqli_query($connection, $sql) or die (mysqli_error($connection));
    $row_customer = mysqli_fetch_assoc($result_customer);
    
    $sql = "select count(*) from customer_dispositions where customer_id = ".$row_customer['id'];
    $result_disposition = mysqli_query($connection, $sql) or die (mysqli_error($connection));
    $row_count = mysqli_fetch_assoc($result_disposition);
    $countx = $row_count['count(*)'];
    if ($countx == 0) {
        $sql = "INSERT INTO `customer_dispositions` (`customer_id`, `contact_date_time`, `disposition`, `user_id`, `extension`, `job_id`) VALUES (".$row_customer['id'].", '".$row['calldate']."', ".$hung_up[$jobs[$phonenumber]].", 0, 0, ".$job.")";
        $sql2 = "UPDATE cdr set userfield2 = 'adjusted' WHERE uniqueid = '".$uniqueid."'";
        echo $sql."\n";
        echo $sql2."\n";
        mysqli_query($connection, $sql);
        mysqli_query($connection, $sql2);
        //exit(0);
    }
//    print_r($row_customer);
}
