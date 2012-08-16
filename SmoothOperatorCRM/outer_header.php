<frameset cols="*, 150" border="1">
<frame name="CRM" id="CRM" src="index.php" >
<?if (isset($_GET['debug'])) {
    ?>
    <frame name="phone" id="phone" src="phono.php?debug=1" >
    <?
} else {
    ?>
    <frame name="phone" id="phone" src="phono.php" >
    <?
}
?>
</frameset>