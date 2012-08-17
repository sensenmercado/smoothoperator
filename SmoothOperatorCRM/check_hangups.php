<?
/* Connect to the database */
require "config/db_config.php";

/* Include sanitize functions */
require "functions/sanitize.php";

$sql = "SELECT * FROM hangups WHERE extension = ".sanitize($_GET['extension']);
$result = mysqli_query($connection, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo "1";
    mysqli_query($connection, "DELETE FROM hangups where id = ".$row['id']);
}
?>