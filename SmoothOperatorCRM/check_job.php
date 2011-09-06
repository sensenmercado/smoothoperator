<?
/* Connect to the database */
require "config/db_config.php";

/* Include sanitize functions */
require "functions/sanitize.php";

$sql = "SELECT * FROM job_members, jobs WHERE job_members.job_id = jobs.id and job_members.user_id = ".sanitize($_GET['user_id']);
$result = mysqli_query($connection, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo $row['name'];
} else {
    echo "No Job";
}

?>