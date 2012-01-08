<?
if (isset($_GET['delete'])) {
    // TODO: finish this
}
if (isset($_GET['add_disposition'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $sql = "INSERT INTO job_dispositions (`job_id`, `text`) VALUES (".sanitize($_POST['job_id']).",".sanitize($_POST['text']).")";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    $new_id = mysqli_insert_id($connection);
    echo $new_id;
    exit(0);
}
if (isset($_GET['delete_disposition'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $result = mysqli_query($connection, "DELETE FROM job_dispositions WHERE id = ".sanitize($_GET['delete_disposition']));
    exit(0);
}
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
    /* Add a job to the database */
    $sql = "INSERT INTO jobs (name, description) VALUES (".sanitize($_POST['name']).", ".sanitize($_POST['description']).")";
    mysqli_query($connection, $sql);
    
    /* Find out the newly created job ID */
    $job = mysqli_insert_id($connection);
    
    /* If there is a SmoothTorque host/user/pass connect to it */
    if (strlen($config_values['smoothtorque_db_host']) > 0) {
        /* Create a queue */
        /* Create a campaign */
    }

    
    /* Redirect to edit the new job */
    redirect("jobs.php?job_id=".$job,0);
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
    <?
    /* If there is a SmoothTorque host/user/pass show campaign types */
    if (strlen($config_values['smoothtorque_db_host']) > 0) {
        
        /* Make sure there is a user in SmoothTorque we can use to create the campaign under */
        $link = mysql_connect($config_values['smoothtorque_db_host'], $config_values['smoothtorque_db_user'], $config_values['smoothtorque_db_pass']) or die(mysql_error());
        $result = mysql_query("SELECT username, campaigngroupid FROM SineDialer.customer");
        echo '<tr><th>Dialer Account:</th><td><select name="st_account">';
        while($row = mysql_fetch_assoc($result)) {
            echo '<option value="'.$row['username']."!".$row['campaigngroupid'].'">'.$row['username'].'</option>';
        }
        echo '</select></td></tr>';
        
        
        ?>
        <tr><th>Dialer Campaign Type:</th>
        <td>
        <select name="st_campaign_type">
        <option value="-1" SELECTED>Please chose a type of campaign...</option>
        <option value="0" title="No phone calls are made">Load Simulation</option>
        <option value="1" title="Only leave a message for answering machines, hang up when a person answers">Answer Machine Only</option>
        <option value="2" title="Automatically send a person straight through to the call center">Immediate Live</option>
        <option value="4" title="Play a message to a person, hang up for answering machines">Press 1 Live Only</option>
        <option value="5" title="Put a person straight through to the call center, and leave a message for the answer machines">Immediate Live and Answer Machine</option>
        <option value="3" title="Play a message to a person, if they press 1, put them through to the call center. Leave a message for answering machines">Press 1 Live and Answer Machine</option>
        <option value="6" title="As soon as a number is connected, transfer it to a staff memeber"> Direct Transfer</option>
        <option value="7" title="When a call is answered, play back the message and then hang up"> Immediate Message Playback</option>
        </select>
        </td>
        </tr>
        <?
    }
    ?>
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

<script>
var counter = 0;
var entries_to_ids=new Array();


function delete_entry_from_database(item) {
    //alert("Deleting item "+item+" from script <?=$_GET['edit']?> (id "+entries_to_ids[parseInt(item)]+")");
    new Ajax.Request('jobs.php?delete_disposition='+entries_to_ids[parseInt(item)], {onSuccess: function(transport){
    jQuery('#status_bar').text("Deleted Disposition");
    jQuery('#status_bar').fadeIn(1000);    
    jQuery('#status_bar').fadeOut(5000);    
                     }});
}

function delete_entry(item) {
    /* The item number is the number in the script starting from one - bearing in mind that there may be deleted
     entries.  I.E. Item 1 may not be id 1.  If you had three entries and you delete id 1 and id 2 then item 1 would
     be id 2 (id is zero based) */
    Dialog.confirm('Are you sure you want to remove this disposition', {className:'alphacube', width:400, 
                   okLabel: 'Yes', cancelLabel: 'No',
                   onOk:function(win){
                   jQuery("#entry"+item).remove();
                   delete_entry_from_database(item);
                   return true;
                   }
                   }
                   );
}

function save_disposition(statement, divName){
    new Ajax.Request('jobs.php?add_disposition=1',{parameters: {job_id: <?=$_GET['job_id']?>, text: statement}, onSuccess: function(transport){
                     if (transport.responseText) {
                     var response = transport.responseText;
                     entries_to_ids[counter] = parseInt(response);
                     jQuery('#status_bar').text("Saved Disposition");
                     jQuery('#status_bar').fadeIn(1000);    
                     jQuery('#status_bar').fadeOut(5000);

                     }
                     }
                     });
}

function add_disposition(statement, divName){
    counter++;
    var newdiv = document.createElement('div');
    newdiv.innerHTML = "<div class='disposition_entry' id='entry"+counter+"'><a href='#' onclick='delete_entry("+counter+");'><img src='images/delete.png' alt='Delete' width='16' height='16' align='right'></a>"+statement+"</div>";
    document.getElementById(divName).appendChild(newdiv);
}


function add_new_disposition() {
    Dialog.confirm('Disposition Text: <input type="text" id="disposition_text">', {className:'alphacube', width:400, 
                   okLabel: 'Save', cancelLabel: 'cancel',
                   onOk:function(win){
                   save_disposition(jQuery('#disposition_text').val(), 'dynamicInput');
                   add_disposition(jQuery('#disposition_text').val(), 'dynamicInput');
                   return true;
                   }
                   }
                   );
}
</script>

<div class='panel_b'>
<h2>Job Details</h2>
Script: <select name="script" id="script" onchange="new Ajax.Request('jobs.php?save_script='+getUrlVars()['job_id'],{parameters: {script: jQuery('#script').val()}, onSuccess: function(transport){                     jQuery('#status_bar').text('Saved Script');jQuery('#status_bar').fadeIn(1000);jQuery('#status_bar').fadeOut(5000);if (transport.responseText) {var response = transport.responseText;alert(response);}}});">
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
Dispositions: <a href="#" onclick="add_new_disposition();"><img src="images/add.png">&nbsp;Add Disposition</a>
<br />
<div id="dynamicInput"></div>
<?
$result_entries = mysqli_query($connection, "SELECT * FROM job_dispositions WHERE job_id = ".sanitize($_GET['job_id']));
$x = 0;
if (mysqli_num_rows($result_entries) > 0) {
    while ($row_entries = mysqli_fetch_assoc($result_entries)) {
        $x++;
        ?>
        <script language="javascript">
        entries_to_ids[<?=$x?>] = <?=$row_entries['id']?>;
        add_disposition('<?=$row_entries['text']?>', 'dynamicInput');
        </script>
        <?
    }
}
?>
<br />
</div>

<script>
jQuery( "#name" ).eip( "jobs.php?save_field=1" );
jQuery( "#description" ).eip( "jobs.php?save_field=1" );
</script>

