<?
$rounded[] = "div.thin_700px_box";
require "header.php";

?>
<div id="loading" class="thin_700px_box">
    <img src="images/sq_progress.gif">
    Please wait, attempting to connect to the module repository...
    
</div>
<?
flush();
?>
<div id="loading" class="thin_700px_box">
Module Repository
</div>
<div class="thin_700px_box">
    <table>
        <?
        $url = "http://www.venturevoip.com/so_modules.php";
        $f = 1;
        $c = 2;//1 for header, 2 for body, 3 for both
        $r = NULL;
        $a = NULL;
        $cf = NULL;
        $pd = NULL;
        $page = open_page($url,$f,$c,$r,$a,$cf,$pd);
        $line = split("\n",$page);
        //print_pre($page);
        ?>
        <script type="text/javascript">
            if(document.all){ //IS IE 4 or 5 (or 6 beta)
                eval("document.all.loading.style.display = none");
            }
            if (document.layers) { //IS NETSCAPE 4 or below
                document.layers['loading'].display = 'none';
            }
            if (document.getElementById &&!document.all) {
                hza = document.getElementById('loading');
                hza.style.display = 'none';
            }
        </script>
        <?
        for ($i = 0;$i<sizeof($line);$i++) {
            if (substr($line[$i],0,4) == "Name") {
                $name = substr($line[$i],6);
                $icon = substr($line[$i+1],6);
                $description = substr($line[$i+2],12);
                $file = trim(substr($line[$i+3],10));
                $per_month = trim(substr($line[$i+4],11));
                $purchase = trim(substr($line[$i+5],10));
                $credit = trim(substr($line[$i+6],9));
                if (strlen(trim($file)) > 0 && file_exists("./modules/".trim($file))) {
                        ?>
                        <tr class="installed">
                            <td class="column1x">
                                <img class="column1x" src="./images/icons/32x32/<?=$icon;?>.png" border="0">
                            </td>
                            <td valign="center" style="padding: 20px">
                                <b><?=$name;?></b>
                            </td>
                            <td style="padding: 20px">
                                <p align="left">
                                    <?=$description;?>
                                </p>
                                <p align="left">
                                    More Information: <a href="<?=$credit;?>"><?=$credit;?></a><br />

                                </p>
                            </td>
                            <td>
                        <?
                        //box_button("Uninstall ".$name,"delete","./view_modules.php?action=uninstall&filename=$file","");
                        echo '<a href="./view_modules.php?action=uninstall&filename='.$file.'" title="Remove this module"><img border="0" align="left" src="images/icons/32x32/filesystems/trashcan_full.png"></a>';
                        ?>
                        </td>
                        </tr>
                        <?

                } else {
                    ?>
                    <tr class="notinstalled">
                    
                    <td class="column1x">
                        <img class="column1x" src="./images/icons/32x32/<?=$icon;?>.png" border="0">
                    </td>
                    <td class="column1x" valign="center" style="padding: 20px">
                        <b><?=$name;?></b>
                    </td>
                    <td style="padding: 20px">
                        <p align="left">
                            <?=$description;?>
                        </p>
                        <p align="left">
                            More Information: <a href="<?=$credit;?>"><?=$credit;?></a><br />

                        </p>
                    </td>
                    <td>


                    <?
                    echo '<a href="./add_module.php?action=install&filename='.$file.'" title="Install this module"><img border="0" align="left" src="images/icons/32x32/actions/edit_add.png"></a>';
                    ?>
                    </td>
                    </tr>
                    <?
                }
            }
        }
        ?>




    </table>
</div>
<?
require "footer.php";
?>
