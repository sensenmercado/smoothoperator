<?
if (isset($_GET['save_members'])) {
    //    print_r($_POST);
    require "config/db_config.php";
    require "functions/sanitize.php";
    
    $sql = "DELETE FROM job_members WHERE job_id = ".sanitize($_GET['save_members']).";";
    //echo $sql."\n";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    
    $exploded = explode(",",trim($_POST['members']));
    $sql2 = "REPLACE INTO job_members (job_id, user_id) VALUES ";
    $sql3 = "";
    foreach ($exploded as $member) {
        //echo strlen($member)."\n";
        $sql3 .= "(".sanitize($_GET['save_members']).", ".sanitize(substr(trim($member),5))."),";
        
    }
    if (strlen($sql3) > 0) {
        $sql = $sql2.substr($sql3,0,strlen($sql3)-1);
        $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    }
    //echo $sql."\n";
    
    exit(0);
}
if (!isset($_GET['job_id'])) {
    $rounded[] = "div.box";
    require "header.php";
    ?>
    <div class="box">
    <?
    $result = mysqli_query($connection, "SELECT id, name, description FROM jobs") or die(mysqli_error($connection));;
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <a href="jobs.php?job_id=<?=$row['id']?>"><img src="images/pencil.png" border="0">&nbsp;<?=$row['name']?></a><br />
            <?
            
        }
    }
    ?>
    </div>
    
    <?
    require "footer.php";
    exit(0);
}
$rounded[] = 'div.panel_l';
$rounded[] = 'div.panel_r';
require "header.php";

if (isset($_GET['add'])) {
    require "footer.php";
    exit(0);
}

?>
<script type='text/javascript' src='js/multiselect.js'></script>
<?

$result = mysqli_query($connection, "SELECT id FROM users");
while ($row = mysqli_fetch_assoc($result)) {
    $all_user_ids[] = $row['id'];
}

$result = mysqli_query($connection, "SELECT id FROM users, job_members WHERE users.id = job_members.user_id AND job_members.job_id = ".sanitize($_GET['job_id']));
while ($row = mysqli_fetch_assoc($result)) {
    $in_this_job_ids[] = $row['id'];
}

if (count($in_this_job_ids) > 0) {
    $not_in_job = array_diff($all_user_ids, $in_this_job_ids);
} else {
    $not_in_job = $all_user_ids;
}

$not_used = "";
if (count($not_in_job) > 0) {
    foreach ($not_in_job as $not) {
        $not_used .= $not.",";
    }
    $not_used = substr($not_used,0,strlen($not_used)-1);
}
?>


<table>
<tr>
<td>
<div class='panel_l'>
<h2>In this job</h2>

<ul class="swappers" id="list_1">
<?
$result = mysqli_query($connection, "SELECT * FROM users, job_members WHERE users.id = job_members.user_id AND job_members.job_id = ".sanitize($_GET['job_id']));
while ($row = mysqli_fetch_assoc($result)) {
    echo '<li id="user_'.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].' ('.$row['username'].')</li>';
}
?>
</ul>
</div>
</td>
<td>
<div class='panel_r'>
<h2>Not in this job</h2>

<ul class="swappers" id="list_2">
<?
if (count($not_in_job) > 0) {
    $result = mysqli_query($connection, "SELECT * FROM users WHERE id IN (".$not_used.")") or die(mysqli_error($connection));
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<li id="user_'.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].' ('.$row['username'].')</li>';
    }
}
?>
</ul>
</div>
</td>
</tr>
</table>