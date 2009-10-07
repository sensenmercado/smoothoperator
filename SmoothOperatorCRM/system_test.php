<?
$rounded[] = "div.thin_700px_box";
require "header.php";
?>
<div class="thin_700px_box">

<?
if (isset($_GET['test'])) {
    switch ($_GET['test']) {
        case "call":
            /* Make a test call */
            if (!isset($_GET['channel'])) {
                ?>
                <form action="system_test.php" method="GET">
                    <input type="hidden" name="test" value="<?=$_GET['test']?>">
                    Channel: <input type="text" name="channel"><br />
                    Context: <input type="text" name="context"><br />
                    Extension: <input type="text" name="extension"><br />
                    CallerID: <input type="text" name="callerid"><br />
                    AccountCode: <input type="text" name="accountcode"><br />
                    <input type="submit" value="Make Call">
                </form>
                <?
                require "footer.php";
                exit(0);
            } else {
                asterisk_make_call($config_values['manager_host'],
                                   $config_values['manager_user'],
                                   $config_values['manager_pass'],
                                   $_GET['channel'],
                                   $_GET['context'],
                                   $_GET['extension'],
                                   $_GET['callerid'],
                                   $_GET['accountcode']);
                require "footer.php";
                exit(0);
            }
            break;
    }
}

?>
<a href="system_test.php?test=call">Make a test call</a><br />
<a href="system_test.php?test=lookup_campaign">Lookup Campaign Names</a><br />
<a href="system_test.php?test=create_campaign">Create a Campaign</a><br />
<a href="system_test.php?test=start_campaign">Start a Campaign</a><br />
</div>
<?
require "footer.php";
?>
