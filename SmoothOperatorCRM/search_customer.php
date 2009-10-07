<?
require "header.php";
?>
<div class="thin_700px_box">
<img src="images/icons/32x32/actions/find.png"><br />
<br />
<form action="get_customer.php" method="GET">
    Phone Number: <input type="text" name="phone_number"><br />
    <br />
    <input type="submit" value="Find Number">
</form>
</div>
<?
require "footer.php";
?>
