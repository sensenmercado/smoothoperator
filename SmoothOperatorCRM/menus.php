<?
if (isset($_GET['listItem'])) {
    foreach ($_GET['listItem'] as $position => $item) {
        $sql = "UPDATE `menu_items` SET `menu_order` = $position WHERE `id` = $item";
        require "config/db_config.php";
        $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    }
    //print_r ($sql);
    require "functions/urls.php";
    redirect("menus.php", 0);
    exit(0);
}
//$rounded[] = 'div.panel_r';
//$rounded[] = "li.xxxx";
require "header.php";
?>
<script type="text/javascript">
// When the document is ready set up our sortable with it's inherant function(s)
$(document).ready(function() {
  $("#test-list").sortable({
    handle : '.handle',
    update : function () {
      var order = $('#test-list').sortable('serialize');
      $("#info").load("menus.php?"+order);
    }
  });
});
</script>
<div id="info" ></div>

<div class="xxxx"  style="background: #cdf;width: 600px;margin-top: 10px;padding:5px;">
<center>
<a href="menus.php?add=1"><img src="images/user.png">&nbsp;Add Menu Item</a>&nbsp;
</center>
</div>


<div class="xxxx"  style="background: #cdf;width: 400px;margin-top: 30px;padding:40px;">


<ul id="test-list" class="xxxx">


<?
$result = mysqli_query($connection, "SELECT id, menu_order, menu_text, link FROM menu_items ORDER BY menu_order") or die(mysqli_error($connection));
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<li class="xxxx" id="listItem_'.$row['id'].'" align="left">';
        ?><img src="images/arrow.png" alt="move" width="16" height="16" class="handle" /> <?
        /*?><img src="images/icons/32x32/actions/ledlightblue.png" alt="move" width="16" height="16" class="handle" /> <?*/
        echo '<strong>'.$row['menu_text'].'</strong> (<a href="menus.php?edit='.$row['id'].'">'.$row['link'].')</a></li>';
    }
}
?>

</ul>


<form action="menus.php" method="post" name="sortables">
  <input type="hidden" name="test-log" id="test-log" />
</form>
</div>
<?

require "footer.php";
?>
