<?
$current_directory = dirname(__FILE__);
if (isset($override_directory)) {
        $current_directory = $override_directory;
}


if (isset($_GET[filename])) {
    require "config/db_config.php";
    require "functions/functions.php";

    $xml = simplexml_load_file("./modules/".$_GET[filename]);
    $module_type = $xml->type;

    if ($_GET[action] == "install"||$_GET[action] == "uninstall") {
        $menus = $xml->menu;

        foreach ($menus->item as $menu_item) {
            $link = sanitize("".$menu_item->link, true);
            $use_iframe = sanitize("".$menu_item->use_iframe);
            $security_level = sanitize("".$menu_item->security_level);
            foreach ($menu_item->text as $text_item) {
                //print_pre($text_item);
                foreach ($text_item as $key=>$value) {
                    //echo "Language: $key String: $value Link $link Security_Level: $security_level Use Iframe: $use_iframe<br />";
                    $menu_text = sanitize("".$value);
                    $language = sanitize("".$key);
                    if ($_GET[action] == "install") {
                        /* ************************************************** */
                        /* INSTALL A MENU ITEM                                */
                        /* ************************************************** */

                        $sql = "INSERT INTO menu_items (menu_text, language, security_level, link, use_iframe) VALUES (";
                        $sql .=$menu_text.",".$language.",".$security_level.",".$link.",".$use_iframe;
                        $sql.=")";

                        /* ************************************************** */
                        /* CREATE A PAGE                                      */
                        /* ************************************************** */

                        unset($file_contents);
                        if (isset($menu_item->page)) {
                            $file_contents = base64_decode($menu_item->page->content);
                            $filename = dirname(__FILE__)."/".$menu_item->link;

                            if (file_exists($filename)) {
                                /* File already Exists */
                                //echo "File $filename already exists";
                                //exit(0);
                            } else {
                                $fp = fopen($filename, w);
                                if (!$fp) {
                                    /* File creation failed */
                                    //echo "File creation failed";
                                } else {
                                    /* File creation succeeded */
                                    if (fwrite($fp, $file_contents)) {
                                        /* Wrote Successufully */
                                        //echo "Wrote";
                                    } else {
                                        /* Didn't write successfully */
                                        //echo "Didn't";
                                    }
                                    fclose($fp);
                                }
                            }
                        }



                    } else if ($_GET[action] == "uninstall") {
                        /* ************************************************** */
                        /* REMOVE A MENU ITEM                                 */
                        /* ************************************************** */

                        $sql = "DELETE FROM menu_items WHERE menu_text=$menu_text AND language=$language AND link=$link";


                        if (isset($menu_item->page)) {
                            $filename = dirname(__FILE__)."/".$menu_item->link;
                            if (strlen($filename) > 3) {
                                /* Just in case the impossible happens don't  */
                                /* unlink the root - should be impossible but */
                                /* better safe than sorry                     */
                                unlink($filename);
                            }
                        }

                    }
                    //echo $sql."<br />";

                    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
                }
            }
        }

        //$menu_name =
        //print_pre($xml);
        //exit(0);
    }

    if ($module_type == "extension") {
        if ($_GET[action] == "install") {
            redirect("modules.php",0);
        } else if ($_GET[action] == "uninstall") {
            //flush();
            exec("rm ./modules/".escapeshellarg($_GET[filename]));
            redirect("modules.php",0);
        } else {
            print_pre($xml);

            echo "Context: $xml->context<br />";
            echo "Extension: $xml->extension<br />";
            echo "Priority: $xml->priority<br />";
            foreach($xml->line as $line) {
                echo "Line: $line<br />";
            }
            foreach($xml->variable as $variable) {
                echo "variable: <br /><pre>";
                print_r($variable);
                echo "</pre>";
            }
        }
    } else if ($module_type == "commands") {
        //print_pre($xml);
        if ($_GET[action] == "install") {
            $fp = fopen("spool/commands", 'w');
            foreach ($xml->install->command as $command) {
                unset($result);
                //echo "Command: $command<br />";
                fwrite($fp, $command."\n");
            }
            fclose($fp);
            redirect("modules.php",0);
        } else if ($_GET[action] == "uninstall") {
            $fp = fopen("spool/commands", 'w');
            foreach ($xml->uninstall->command as $command) {
                unset($result);
                //echo "Command: $command<br />";
                fwrite($fp, $command."\n");
            }
            fclose($fp);
            exec("rm ./modules/".escapeshellarg($_GET[filename]));
            redirect("modules.php",0);
        } else {
            print_pre($xml);
        }
        } else {
            echo "<b>Unknown Module --==$command==-- </b><pre>";
            print_r($xml);
            echo "</pre>";
    }
} else {
require "header.php";
box_start();
echo "<p><b>View Installed Modules</b></p>";
box_end();
box_start();

if ($handle = opendir('./modules')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $xml = simplexml_load_file("./modules/".$file);
            if (isset($xml->icon)) {
                $icon = $xml->icon;
            } else {
                $icon = "application";
            }
            box_button($xml->name, $icon,"./view_modules.php?filename=$file",$xml->description);
        }
    }
}
}

exit(0);
if ($module_type == "extension") {
    if (is_array($lines)) {
        echo "Array";
    } else {
        echo "Not array";
    }
}


foreach($xml->children() as $child)
  {
  echo $child->getName() . ": " . $child . "<br />";
  }

box_button("Modules","application_add","./modules.php","Go back to the modules page");
box_end();

require "footer.php";
?>
