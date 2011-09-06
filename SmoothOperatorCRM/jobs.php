<?
if (isset($_GET['save_field'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $url = parse_url($_POST['url']);
    $exploded = explode("=",$url['query']);
    $type = $exploded[0];
    $id = sanitize($exploded[1]);
    $field = sanitize($_POST['id'], false);
    $value = sanitize($_POST['new_value']);
    
    $sql = "UPDATE jobs SET $field = $value WHERE id = $id";
    $result = mysqli_query($connection, $sql);
    $response['is_error'] = false;
    $response['error_string'] = mysqli_error($connection);;
    $response['html'] = $_POST['new_value'];
    echo json_encode($response);
    exit(0);
}
if (isset($_GET['save_script'])) {
    //    print_r($_POST);
    require "config/db_config.php";
    require "functions/sanitize.php";
    $sql = "UPDATE jobs SET script_id=".sanitize($_POST['script'])." WHERE id = ".sanitize($_GET['save_script']);
    $result = mysqli_query($connection, $sql);
    exit(0);    
}    
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
        mysqli_query($connection, "DELETE FROM job_members WHERE user_id = ".sanitize(substr(trim($member),5)));
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
if (isset($_GET['save'])) {
    require "header.php";
    $sql = "INSERT INTO jobs (name, description) VALUES (".sanitize($_POST['name']).", ".sanitize($_POST['description']).")";
    mysqli_query($connection, $sql);
    redirect("jobs.php",0);
    require "footer.php";
    exit(0);
}
if (isset($_GET['add'])) {
    require "header.php";
    ?>
    <br />
    <form action="jobs.php?save=1" method="post">
    <table class="sample">
    <tr>
    <th>Job Name</th>
    <td><input type="text" name="name"></td>
    </tr>
    <tr>
    <th colspan="2">Job Description</th></tr>
    <tr>
    <td colspan="2"><textarea name="description"></textarea></td>
    </tr>
    <tr>
    <td colspan="2"><input type="submit" value="Add Job"></td>
    </tr>
    </table>
    </form>
    <?
    require "footer.php";
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
            <a href="jobs.php?job_id=<?=$row['id']?>"><img src="images/pencil.png" border="0">&nbsp;<?=$row['name']?></a><a href="jobs.php?delete=<?=$row['id']?>"><img src="images/delete.png" border="0"></a><br />
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
$rounded[] = 'div.panel_t';
$rounded[] = 'div.panel_b';
require "header.php";

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
$result_x = mysqli_query($connection, "SELECT * FROM jobs WHERE id = ".sanitize($_GET['job_id']));
$row_x = mysqli_fetch_assoc($result_x);

?>
<div class='panel_t'>
<h2>Job Title: <span id="name"><?=$row_x['name']?></span></h2>
Job Description: <span id="description"><?=$row_x['description']?></span>
</div>
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



<div class='panel_b'>
<h2>Job Details</h2>
Script: <select name="script" id="script" onchange="new Ajax.Request('jobs.php?save_script='+getUrlVars()['job_id'],{parameters: {script: jQuery('#script').val()}, onSuccess: function(transport){if (transport.responseText) {var response = transport.responseText;alert(response);}}});">
<option value="-1">Please select a script...</option>
<?
$result = mysqli_query($connection, "SELECT * FROM scripts");
while ($row = mysqli_fetch_assoc($result)) {
    
    echo '<option value="'.$row['id'].'" '.($row_x['script_id']==$row['id']?"SELECTED":"").'>'.$row['name'].'</option>';
    //print_pre($row);
}
?></select>
<br />
<br />
Dispositions: 
<br />
<br />
</div>

<script>
jQuery( "#name" ).eip( "jobs.php?save_field=1" );
jQuery( "#description" ).eip( "jobs.php?save_field=1" );
</script>

