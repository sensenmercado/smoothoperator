<?
/* Connect to the database */
require "config/db_config.php";

/* Include sanitize functions */
require "functions/sanitize.php";

$sql = "SELECT * FROM phone_calls WHERE extension = ".sanitize($_GET['extension']);
$result = mysqli_query($connection, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo $row['callerid'];
    mysqli_query($connection, "DELETE FROM phone_calls where id = ".$row['id']);
}
?>