<?
require "header.php";
?>
<div class="xxxx"  style="background: #cdf;width: 300px;margin-top: 30px;padding:40px;">
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
