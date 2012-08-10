<?
require "header.php";

if ($_SESSION['user_level']<100) {
    redirect("index.php");
} else {
    ?>
    <br />
    <br />
    <a href="cdr.php" class="new_button"><img src="images/crystal/apps/pppoeconfig.png" width="96px" height="96px"><br />Call Details</a>
    <?
}
require "footer.php";
?>
