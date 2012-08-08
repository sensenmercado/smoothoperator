<?
require "header.php";

if ($_SESSION['user_level']<100) {
    ?>
    <div class="thin_700px_box">
    <p><img src="images/icons/32x32/actions/messagebox_info.png"><br />
    <br />
    Welcome to <?=stripslashes($config_values['site_name'])?><br />
    <br />
    Please select a menu option from above.</p>
    </div>
    <?
} else {
    ?>
    <br />
    <br />
    <a href="dialer.php" class="new_button"><img src="images/crystal/apps/pppoeconfig.png" width="96px" height="96px"><br />Dialer</a>
    <a href="receive.php" class="new_button"><img src="images/crystal/apps/harddrive.png" width="96px" height="96px"><br />Files</a>
    <a href="jobs.php" class="new_button"><img src="images/crystal/apps/reminders.png" width="96px" height="96px"><br />Jobs</a>
    <br />
    <a href="scripts.php" class="new_button"><img src="images/crystal/apps/package_editors.png" width="96px" height="96px"><br />Scripts</a>
    <a href="users.php" class="new_button"><img src="images/crystal/apps/Login%20Manager.png" width="96px" height="96px"><br />User Accounts</a>
    <a href="config.php" class="new_button"><img src="images/crystal/apps/advancedsettings.png" width="96px" height="96px"><br />Settings</a>
    <br />
    <a href="list_customers.php" class="new_button"><img src="images/crystal/apps/kaddressbook.png" width="96px" height="96px"><br />Numbers</a>
    <a href="reports.php" class="new_button"><img src="images/crystal/apps/kchart.png" width="96px" height="96px"><br />Reports</a>
    <a href="menus.php" class="new_button"><img src="images/crystal/apps/windowlist.png" width="96px" height="96px"><br />Menus</a>
    
    <?
}
require "footer.php";
?>
