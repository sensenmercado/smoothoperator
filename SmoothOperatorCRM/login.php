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
        <input type="text" name ="username">
        <input type="password" name="password">
        <input type="submit" value="login">
    </form>
<?
require "footer.php";
?>