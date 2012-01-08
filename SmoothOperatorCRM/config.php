<?
/* The system configuration works like this.  You have a table in MySQL called */
/* config.  This table has a primary key of parameter and a field called value */
/* There is also another table which contains the strings to describe these    */
/* config values.  This table is called static_text.  It has three fields, the */
/* language (i.e. en_gb etc), the config parameter it is describing and        */
/* the text that you would like to use to describe it.                         */

/* This means that if you need to add a configurable parameter to the site you */
/* can just add it to the database as well as a description for it and it will */
/* be available for usage.                                                     */

$rounded[] = "div.thin_700px_box";
require "header.php";
?>
<div class="thin_700px_box">
<?
if (isset($_GET['save'])) {
    foreach ($_POST as $field=>$value) {
        $sql = "UPDATE config set value = ".sanitize($value)." WHERE parameter = ".sanitize($field);
        //echo $sql;
        $result = mysqli_query($connection, $sql);
        $config_values[$field] = $value;
        if (!$result) {
            $messages[] = "There was an error saving $field: $value to MySQL: ".mysqli_error();
        }
    }
    $_SESSION['messages'] = $messages;
    $_SESSION['config_values'] = $config_values;
    redirect("config.php",0);
    require "footer.php";
    exit(0);
}
$sql = "SELECT * FROM config, static_text WHERE config.parameter = static_text.parameter and static_text.language = ".sanitize($_SESSION['language']);
//echo $sql;
$result = mysqli_query($connection, $sql) or die(mysqli_error());
echo "SVN Revision: ".$_SESSION['revision']."<br />";
echo '<form action="config.php?save=1" method="post"><table class="sample">';
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['parameter'] == 'smoothtorque_db_pass') {
        echo '<tr><th>'.$row['description'].'</th><td><input type="password" name="'.$row['parameter'].'" value="'.stripslashes($config_values[$row['parameter']]).'"></td></tr>';
    } else {
        echo '<tr><th>'.$row['description'].'</th><td><input type="text" name="'.$row['parameter'].'" value="'.stripslashes($config_values[$row['parameter']]).'"></td></tr>';
    }
}
echo '<tr><td colspan="2"><input type="submit" value = "Save Changes"></td></tr>';
echo '</table></form>';
?>
</div>
<?
require "footer.php";
?>
