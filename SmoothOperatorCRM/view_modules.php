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

    $result = mysqli_query($connection, "SELECT id FROM modules WHERE name = ".sanitize($_GET[filename]));
    if (mysqli_num_rows($result) > 0) {
        /* Module entry exists */
        $row = mysqli_fetch_assoc($result);
        $module_id = $row['id'];
    } else {
        $result = mysqli_query($connection, "INSERT INTO modules (name) VALUES (".sanitize($_GET[filename]).")");
        $module_id = mysqli_insert_id();
    }
    if ($_GET[action] == "install") {
        unset($file_contents);
        if (isset($xml->archive)) {
            $file_contents = base64_decode($xml->archive->contents);
            $filename = dirname(__FILE__)."/".$xml->archive->filename;
            $have_file = false;
            if (file_exists($filename)) {
                /* File already Exists */
                $have_file = true;
            } else {
                $fp = fopen($filename, w);
                if (!$fp) {
                    /* File creation failed */
                    echo "File creation of $filename failed";
                    $have_file = false;
                } else {
                    /* File creation succeeded */
                    if (fwrite($fp, $file_contents)) {
                        $have_file = true;
                    } else {
                        $have_file = false;
                    }
                    fclose($fp);
                }
            }
            if ($have_file) {
                require "archive.php";
                $test = new gzip_file($filename);
                $test->set_options(array('overwrite' => 1, 'inmemory' => 0));
                $test->extract_files();

                /* Doesn't create directories when running as inmemory and */
                /* doesn't create the list of files running without...     */
                $test->set_options(array('overwrite' => 1, 'inmemory' => 1));
                $test->extract_files();
                $test->make_list();
                foreach ($test->files as $file) {
                    $sql = "INSERT INTO module_files (module_id, file_name) VALUES ($module_id, ".sanitize($file['name']).")";
                    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
                }
                

                if (isset($xml->patch->base)) {
                    require "phppatcher.php";
                    $patch = new PhpPatcher($xml->patch->base);
                    $patch->Merge(base64_decode($xml->patch->diff));
                    //$patch->msg;
                    $patch->ApplyPatch();
                }
            }
            if (isset($xml->config_options)) {
                foreach ($xml->config_options->option as $option) {
                    unset($sql);
                    /* sanitize needs ""+ because it automatically detects object type */
                    $sql1 = "INSERT INTO config (parameter, value) VALUES (".sanitize("".$option->name, true).", ".sanitize("".$option->value, true).")";
                    $sql2 = "INSERT IGNORE INTO static_text (parameter, language, description) VALUES (".sanitize("".$option->name).",'en_gb', ".sanitize("".$option->text).")";
                    $result = mysqli_query($connection, $sql1);
                    $result = mysqli_query($connection, $sql2);

                }
            }
            //exit(0);
        }
    } else if ($_GET[action] == "uninstall") {
        $sql = "SELECT id FROM modules WHERE name = ".sanitize($_GET['filename']);
        $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $module_id = $row['id'];
            $result = mysqli_query($connection, "SELECT file_name FROM module_files WHERE module_id = $module_id order by LENGTH(file_name) desc");
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $file_name = $row['file_name'];
                    //echo $file_name."<br />";
                    if (substr($file_name, strlen($file_name) - 1) == "/") {
                        rmdir($file_name);
                    } else {
                        unlink($file_name);
                    }
                    $sql = "DELETE FROM module_files WHERE module_id = $module_id AND file_name = ".sanitize($file_name);
                    $result_delete = mysqli_query($connection, $sql);
                }
            }
        }


        //exit(0);

        unset($filename);
        if (isset($xml->archive)) {
            $filename = dirname(__FILE__)."/".$xml->archive->filename;
            if (strlen($filename) > 3) {
                /* Just in case the impossible happens don't  */
                /* unlink the root - should be impossible but */
                /* better safe than sorry                     */
                unlink($filename);
            }
        }
    }


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
                    if ($_GET['action'] == "install") {
                        /* ************************************************** */
                        /* INSTALL A MENU ITEM                                */
                        /* ************************************************** */

                        $sql = "INSERT INTO menu_items (menu_text, language, security_level, link, use_iframe) VALUES (";
                        $sql .=$menu_text.",".$language.",".$security_level.",".$link.",".$use_iframe;
                        $sql.=")";
                        //echo $sql;
                        $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
                        /* ************************************************** */
                        /* CREATE A PAGE                                      */
                        /* ************************************************** */

                        

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



                        unset($filename);
                        if (isset($menu_item->page)) {
                            $filename = dirname(__FILE__)."/".$menu_item->link;
                            if (strlen($filename) > 3) {
                                /* Just in case the impossible happens don't  */
                                /* unlink the root - should be impossible but */
                                /* better safe than sorry                     */
                                unlink($filename);
                            }
                        }
                        //echo $sql."<br />";
                        $sql = "DELETE FROM menu_items WHERE menu_text=$menu_text AND language=$language AND link=$link";
                        $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));

                    }
                    
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
