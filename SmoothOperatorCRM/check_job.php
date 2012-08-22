<?
/* Connect to the database */
require "config/db_config.php";

/* Include sanitize functions */
require "functions/sanitize.php";
if (!isset($_GET['user_id'])) {
    $_GET['user_id'] = $_SESSION['user_id'];
}
$sql = "SELECT * FROM job_members, jobs WHERE job_members.job_id = jobs.id and job_members.user_id = ".sanitize($_GET['user_id']);
$result = mysqli_query($connection, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['job_id'] = $row['job_id'];
    echo $row['name'];
} else {
    echo "No Job";
    $_SESSION['job_id'] = -1;
}

?>