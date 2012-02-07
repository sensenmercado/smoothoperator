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
    draw_progress("Please wait, logging you in...");
    $username = sanitize($_POST['username']);
    $password = sanitize(sha1($_POST['password']));
    $result = mysqli_query($connection, "SELECT security_level, extension, id FROM users WHERE username = $username AND password = $password");
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
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['user_level'] = $security_level;
    $_SESSION['extension'] = $row['extension'];
    $_SESSION['calls'] = 0;
    $_SESSION['messages'] = $messages;
    $_SESSION['revision'] = REVISION;
    $result = mysqli_query($connection, "SELECT parameter, value FROM config");
    while ($row = mysqli_fetch_assoc($result)) {
        $config_values[$row['parameter']] = $row['value'];
    }
    
    $_SESSION['config_values'] = $config_values;
    redirect("index.php", 0);
    require "footer.php";
    exit(0);
}


?>
<div style="background: #cdf url('images/login-bg.jpg');height: 206px;width: 380px;margin-top: 90px;padding:0px;">
<table height="100%" style="padding-top:60px;">
<tr>
<td valign="center">
<form action="<?=$this_page?>" method="post">
<b>Username:</b>
<input class="rounded" type="text" name ="username">
<br /><br />
<b>Password:</b>
<input class="rounded" type="password" name="password">
<br /><br />
<input type="submit" value="login" style="width: 200px">
</form>
</td>
</tr>
</table>
</div>
</div>
</div>
<?
require "footer.php";
?>
