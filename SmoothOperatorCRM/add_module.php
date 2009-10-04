<?
$current_directory = dirname(__FILE__);
if (isset($override_directory)) {
        $current_directory = $override_directory;
}


if (isset($_GET[filename])) {
    require "functions/functions.php";
    // Install new module
    $url = "http://www.venturevoip.com/so_modules/".$_GET[filename];
$f = 1;
$c = 2;//1 for header, 2 for body, 3 for both
$r = NULL;
$a = NULL;
$cf = NULL;
$pd = NULL;
$page = open_page($url,$f,$c,$r,$a,$cf,$pd);
//$page = split("\n",$page);
    //print_pre($page);
    if (!$handle = fopen("./modules/".$_GET[filename], 'w')) {
         echo "Cannot open file ($_GET[filename])";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($handle, $page) === FALSE) {
        echo "Cannot write to file ($_GET[filename])";
        exit;
    }

    //echo "Success, wrote ($page) to file ($_GET[filename])";

    fclose($handle);
    ?>
    <META HTTP-EQUIV=REFRESH CONTENT="0; URL=view_modules.php?action=install&filename=<?=$_GET[filename]?>">
    <?
exit(0);

}

require "header.php";
box_start();
echo "<p><b>Install Modules</b></p>";
box_end();
box_start();

$url = "http://www.venturevoip.com/install_modules.php";
$f = 1;
$c = 2;//1 for header, 2 for body, 3 for both
$r = NULL;
$a = NULL;
$cf = NULL;
$pd = NULL;
$page = open_page($url,$f,$c,$r,$a,$cf,$pd);
$page = split("\n",$page);
//print_pre($page);
for ($i = 0;$i<sizeof($page);$i++) {
    if (substr($page[$i],0,4) == "Name") {
        $name = substr($page[$i],6);
        $icon = substr($page[$i+1],6);
        $description = substr($page[$i+2],12);
        $file = trim(substr($page[$i+3],10));
        if (file_exists("./modules/".trim($file))) {
//            echo "$name is already Installed<br />";
            box_button("Remove ".$name, "delete","./view_modules.php?action=uninstall&filename=$file",$description);
        } else {
            box_button($name, $icon,"./add_module.php?filename=$file",$description);

//            echo "Name: $name<br />Icon: $icon<br />Description: $description<br />FileName: $filename<br /><br />";
        }
    }
}


box_button("Modules","application_add","./modules.php","Go back to the modules page");
box_end();

require "footer.php";
?>
