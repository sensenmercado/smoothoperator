<?
/* Start or continue a logged in session */
session_start();

/* Find out the current location */
$current_directory = dirname(__FILE__);
if (isset($override_directory)) {
    $current_directory = $override_directory;
}

/* Include VentureVoIP Functions */
require "functions/functions.php";

/* Connect to the database */
require "config/db_config.php";

/* Get any messages about missing tables/databases */
$messages = so_check_databases($db_host, $db_user, $db_pass);

/* Include the standard header */
require "header.php";


if (!defined('REVISION')) {
    /* Find out the current location */
    $current_directory = dirname(__FILE__);
    $filename = $current_directory . '/.svn' . "/" . 'entries';
    
    /* Check if there is an svn entries file */
    if (file_exists($filename)) {
        $svn = file($filename);
        if (is_numeric(trim($svn[3]))) {
            $version = $svn[3];
        } else { // pre 1.4 svn used xml for this file
            $version = explode('"', $svn[4]);
            $version = $version[1];
        }
        define ('REVISION', trim($version));
        unset ($svn);
        unset ($version);
    } else {
        define ('REVISION', 0); // default if no svn data avilable
    }
}

/* If someone has started logging in start checking out the system */
if (isset($_POST['username'])) {
    /* First start by checking/creating the necessary databases        */
    //draw_progress("Please wait, logging you in...");
    $username = sanitize($_POST['username']);
    $password = sanitize(sha1($_POST['password']));
    $result = mysqli_query($connection, "SELECT * FROM users WHERE username = $username AND password = $password");
    if (mysqli_num_rows($result) < 1) {
        $messages[] = "Incorrect Username/Password";
        $_SESSION['messages'] = $messages;
        redirect("login.php",0);
        require "footer.php";
        exit(0);
    }
    
    /* If ?debug=1 is added to the login page, run some tests */
    if (isset($_GET['debug'])) {
        $messages[] = "Testing 123";
        $_SESSION['messages'] = $messages;
    }
    
    $row = mysqli_fetch_assoc($result);
    $security_level = $row['security_level'];
    $_SESSION['user_name'] = $_POST['username'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name'] = $row['last_name'];
    $_SESSION['name'] = $row['first_name']." ".$row['last_name'];
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['user_level'] = $security_level;
    $_SESSION['extension'] = $row['extension'];
    $_SESSION['popup_blocker'] = $row['popup_blocker'];
    $_SESSION['calls'] = 0;
    $_SESSION['messages'] = $messages;
    $_SESSION['revision'] = REVISION;
    $result = mysqli_query($connection, "SELECT parameter, value FROM config");
    while ($row2 = mysqli_fetch_assoc($result)) {
        $config_values[$row2['parameter']] = $row2['value'];
    }
    
    $_SESSION['config_values'] = $config_values;
    
    /* Get the agent login details and add them to the session */
    $result_agents = mysqli_query($connection, "SELECT agent_num, pin FROM agent_nums, users WHERE agent_num = users.extension and users.id=".sanitize($_SESSION['user_id'])) or die(mysqli_error($connection));
    $row_agents = mysqli_fetch_assoc($result_agents);
    $_SESSION['agent_num'] = $row_agents['agent_num'];
    $_SESSION['agent_pass'] = $row_agents['pin'];
    
    if ($row['use_softphone'] == 1) {
        redirect("outer_header.php", 0);
    } else {
        redirect("index.php", 0);
    }
    require "footer.php";
    exit(0);
}


?>
<div style="position: relative; #top: -50%;background: #cdf url('images/login_bg.png');height: 200px;width: 476px;margin: 0px;padding:0px;"><center>
<table height="100%" style="padding-top:0px;">
<tr>
<td>
<img src="images/crystal/apps/gpg.png" width="128" height="128">	
</td>
<td valign="center">
<br />
<form action="<?=$this_page?>" method="post">
<span style="font-size: 20px">Username:
<input class="rounded"  style="font-size: 20px; width: 180px" type="text" name ="username">
<br /><br />
Password:</span>
<input class="rounded"  style="font-size: 20px; width: 180px" type="password" name="password">
<br /><br />
<input type="submit" value="Log In" style="font-size: 20px;width: 300px; height: 40px;">
<br /><br />
</form>
</td>
</tr>
</table>
</div>
<?
require "footer.php";
?>
