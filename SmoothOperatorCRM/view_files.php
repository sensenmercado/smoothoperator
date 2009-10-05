<?
$current_directory = dirname(__FILE__);
if (isset($override_directory)) {
        $current_directory = $override_directory;
}

//if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
//@date_default_timezone_set(@date_default_timezone_get());

require "functions/functions.php";
require "config/db_config.php";
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}
session_start();
/* TODO: Use the user_level required for this page */
if ($_SESSION['user_level'] < 10) {
    exit(0);
}
//echo date("H:i:s");
echo '<table class="sample" width="100%">';
echo '<tr><th>File Name</th><th>File Size</th><th>Date Uploaded</th><th>Delete</th></tr>';
$result = mysqli_query($connection, "SELECT location, filename, size, date_imported, id FROM files");
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr><td>';
        echo '<a href="receive.php?id='.$row['id'].'">'.$row['filename']."</a></td><td>";
        echo formatBytes($row['size'])."</td><td>";
        echo $row['date_imported']."</td>";
        echo "<td>";
        ?>
        <a href="receive.php?delete=<?=$row['id']?>">Delete</a>
        <?
        echo "</td>";
        echo '</tr>';
    }
}
echo '</table>';
?>