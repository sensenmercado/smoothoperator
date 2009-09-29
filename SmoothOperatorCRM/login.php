<?php
require "header.php";
if (isset($_POST['username'])) {
    echo "Logging In";
    $_SESSION['user_name'] = $_POST['username'];
    $_SESSION['user_level'] = 100;
    echo "<meta http-equiv='refresh' content='0;URL=index.php'>";
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