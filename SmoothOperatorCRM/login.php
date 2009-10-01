<?php
require "header.php";
if (isset($_POST['username'])) {
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
    $security_level = mysqli_result($result, 0, 'security_level');
    $_SESSION['user_name'] = $_POST['username'];
    $_SESSION['user_level'] = $security_level;
    $_SESSION['messages'] = $messages;

    $result = mysqli_query("SELECT parameter, value FROM config");
    while ($row = mysqli_fetch_assoc($result)) {
        $config_values[$row['parameter']] = $row['value'];
    }

    $_SESSION['config_values'] = $config_values;
    redirect("index.php", 0);
    require "footer.php";
    exit(0);
}


?>
    <form action="<?=$this_page?>" method="post">
        Username: <input type="text" name ="username"><br />
        Password: <input type="password" name="password"><br />
        <input type="submit" value="login">
    </form>
<?
require "footer.php";
?>