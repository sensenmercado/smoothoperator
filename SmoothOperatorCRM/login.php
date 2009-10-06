<?php
require "header.php";
if (isset($_POST['username'])) {
    /* If someone has started logging in start checking out the system */
    /* First start by checking/creating the necessary databases        */
    $messages = so_check_databases($db_host, $db_user, $db_pass);
    draw_progress("Please wait, logging you in...");
    $username = sanitize($_POST['username']);
    $password = sanitize(sha1($_POST['password']));
    $result = mysqli_query($connection, "SELECT security_level FROM users WHERE username = $username AND password = $password");
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
    $_SESSION['user_level'] = $security_level;
    $_SESSION['messages'] = $messages;

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
<div class="xxxx"  style="background: #cdf url('images/login-bg.jpg');height: 206px;width: 300px;margin-top: 30px;padding:40px;">
<table height="100%">
    <tr>
        <td valign="center">
    <form action="<?=$this_page?>" method="post">
        <b>Username:</b> <input class="rounded" type="text" name ="username"><br />
        <br />
        <b>Password:</b> <input class="rounded" type="password" name="password">
        
        </td><td>
        <input class="rounded" type="submit" value="login">
    </form>
    </td>
    </tr>
    </table>
</div>
<?
require "footer.php";
?>
