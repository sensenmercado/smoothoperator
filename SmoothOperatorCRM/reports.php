<?
require "header.php";

if ($_SESSION['user_level']<100) {
    redirect("index.php");
} else {
    ?>
    <br />
    <br />
    <a href="cdr.php" class="new_button"><img src="images/crystal/apps/pppoeconfig.png" width="96px" height="96px"><br />Call Details</a>
    <a href="realtime.php" class="new_button"><img src="images/crystal/apps/xclock.png" width="96px" height="96px"><br />Realtime Status</a>
    <a href="index.php" class="new_button"><img src="images/crystal/apps/kfm_home.png" width="96px" height="96px"><br />Home</a>
    <?
}
require "footer.php";
?>
