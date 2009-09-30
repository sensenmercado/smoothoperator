<?php
require "header.php";
if (isset($_POST['username'])) {
    $messages = so_check_databases($db_host, $db_user, $db_pass);
    draw_progress("Please wait, logging you in...");
    $username = sanitize($_POST['username']);
    $password = sanitize(sha1($_POST['password']));
    $result = mysql_query("SELECT security_level FROM users WHERE username = $username AND password = $password");
    if (mysql_num_rows($result) < 1) {
        $messages[] = "Incorrect Username/Password";
        $_SESSION['messages'] = $messages;
        redirect("login.php",0);
        require "footer.php";
        exit(0);
    }
    $security_level = mysql_result($result, 0, 'security_level');
    $_SESSION['user_name'] = $_POST['username'];
    $_SESSION['user_level'] = $security_level;
    $_SESSION['messages'] = $messages;
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