<?
require "header.php";
$id = sanitize($_GET['id']);
$result = mysqli_query($connection, "SELECT link, security_level FROM menu_items WHERE id = ".$id);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $link = $row['link'];
    if ($row['security_level'] > $_SESSION['user_level']) {
        //$messages[]=
        $_SESSION['messages'][] = "You have tried to access a page you have no permission for";
        redirect("index.php");
    }
}
?>
<iframe id = "if_menu" src="<?=$link?>" width="100%" height="100%" border="0" style="border: none">
        <p>Your browser does not support iframes.</p>
    </iframe>
<?
require "footer.php";
?>
