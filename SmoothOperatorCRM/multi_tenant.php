<?
require "header.php";
$result = mysqli_query($connection,"SELECT * FROM SmoothOperator.multitenant");
while ($row = mysqli_fetch_assoc($result)) {
    print_pre($row);
}
require "footer.php";
?>