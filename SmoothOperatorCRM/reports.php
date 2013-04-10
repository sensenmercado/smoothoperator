<?
require "header.php";

if ($_SESSION['user_level']<100) {
    redirect("index.php");
} else {
    ?>
    <br />
    <br />
    <a href="cdr.php" class="new_button"><img src="images/crystal/apps/pppoeconfig.png" width="96px" height="96px"><br />Call Details</a>
    <a href="realtime.php" class="new_button"><img src="images/crystal/apps/personal.png" width="96px" height="96px"><br />Agent Status</a>
    <a href="dispositions.php" class="new_button"><img src="images/crystal/apps/download.png" width="96px" height="96px"><br />Download Dispositions</a><br />
    <a href="script_results.php" class="new_button"><img src="images/crystal/apps/package_editors.png" width="96px" height="96px"><br />Script Results</a>
<?/*    <a href="drawpie.php" class="new_button"><img src="images/crystal/apps/blockdevice.png" width="96px" height="96px"><br />Number Statuses</a>*/?>
    <a href="report_dispositions.php" class="new_button"><img src="images/crystal/apps/Volume%20Manager.png" width="96px" height="96px"><br />Disposition Chart</a>
    <a href="report_agent_utilisation.php" class="new_button"><img src="images/crystal/apps/xclock.png" width="96px" height="96px"><br />Agent Dispositions</a><br />
    
    <a href="index.php" class="new_button_home"><img src="images/crystal/apps/kfm_home.png" width="96px" height="96px"><br />Home</a>
    <?
}
require "footer.php";
?>
