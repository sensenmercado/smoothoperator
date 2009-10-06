<?
if (isset($_GET['listItem'])) {
    /* This is used when someone drags a menu to a different location */
    /* It updates the order of the menu items.                        */
    foreach ($_GET['listItem'] as $position => $item) {
        $sql = "UPDATE `menu_items` SET `menu_order` = $position WHERE `id` = $item";
        require "config/db_config.php";
        $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    }

    require "functions/urls.php";   /* Used to get the redirect function */
    redirect("menus.php", 0);       /* Cause the menus to be reloaded    */
    exit(0);
}

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

<!-- used to receive the result of moving the items -->
<div id="info"></div>

<!-- The links to create a new menu item -->
<div class="xxxx"  style="background: #cdf;width: 600px;margin-top: 10px;padding:5px;">
    <center>
        <a href="menus.php?add=1"><img src="images/user.png">&nbsp;Add Menu Item</a>&nbsp;
    </center>
</div>

<!-- This is the actual draggable item list for menus - created from the db -->
<div class="xxxx"  style="background: #cdf;width: 600px;margin-top: 30px;padding:10px;">
    <ul id="test-list" class="xxxx">
        <?
        /* TODO: USE ALL LANGUAGES */
        $result = mysqli_query($connection, "SELECT id, menu_order, menu_text, link FROM menu_items WHERE language = 'en' AND visible = 1 AND child_of = -1 ORDER BY menu_order") or die(mysqli_error($connection));
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<li class="xxxx" id="listItem_'.$row['id'].'" align="left">';
                echo '<img src="images/arrow.png" alt="move" width="16" height="16" class="handle" />';
                echo '<strong>'.$row['menu_text'].'</strong> (<a href="menus.php?edit='.$row['id'].'">'.$row['link'].')</a></li>';
            }
        }
        ?>
    </ul>
</div>
<?

require "footer.php";
?>
