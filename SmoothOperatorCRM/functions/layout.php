<?
if (!function_exists('get_links')) {
    function get_links($user_level, $connection, $visible, $child_of = -1, $sub_menu_only = false) {
        /* TODO: make language selectable */
        if ($sub_menu_only == true) {
            $result = mysqli_query($connection, "SELECT * FROM menu_items WHERE security_level <= ".sanitize($user_level)." and language = 'en' and visible = ".sanitize($visible)." ORDER BY menu_order") or die(mysqli_error($connection));
        } else if ($visible == "1") {
            $result = mysqli_query($connection, "SELECT * FROM menu_items WHERE security_level <= ".sanitize($user_level)." and language = 'en' and child_of = ".sanitize($child_of)." and visible = ".sanitize($visible)." ORDER BY menu_order") or die(mysqli_error($connection));
        } else {
            $result = mysqli_query($connection, "SELECT * FROM menu_items WHERE security_level <= ".sanitize($user_level)." and language = 'en' and visible = ".sanitize($visible)." ORDER BY menu_order") or die(mysqli_error($connection));
        }
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $menu_names[] = $row['menu_text'];
                if ($row['use_iframe'] == 1) {
                    $menu_links[] = "show_page.php?id=".$row['id'];
                } else {
                    $menu_links[] = $row['link'];
                }
            }
        }
        $retval[0] = $menu_names;
        $retval[1] = $menu_links;
        return $retval;
    }
}
if (!function_exists('draw_progress')) {
    function draw_progress($message = "") {
        if (isset($message)) {
            echo $message."<br />";
        }
        echo '<img src="images/progress.gif" border="0">';
    }
}
if (!function_exists('clean_field_name')) {
    function clean_field_name($field) {
        return ucwords(strtolower(str_replace("_", " ",$field)));
    }
}

if (!function_exists('get_menu_items') ) {
    function get_menu_items ($user_level, $connection) {
        return (get_links($user_level, $connection, 1));
        /*
        $result = mysqli_query($connection, "SELECT * FROM menu_items WHERE security_level <= ".sanitize($user_level)." and language = 'en' and visible = 1 ORDER BY menu_order") or die(mysqli_error($connection));
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $menu_names[] = $row['menu_text'];
                if ($row['use_iframe'] == 1) {
                    $menu_links[] = "show_page.php?id=".$row['id'];
                } else {
                    $menu_links[] = $row['link'];
                }

            }
        }
        $retval[0] = $menu_names;
        $retval[1] = $menu_links;
        return $retval;*/
    }
}

if (!function_exists('box_start') ) {
     function box_start($size = "400") {
        echo '<div id="box" style="width:'.$size.'px;"><!--- box border --><div id="lb"><div id="rb"><div id="bb"><div id="blc"><div id="brc"><div id="tb"><div id="tlc"><div id="trc"><div id="boxcontent">';
     }
}
if (!function_exists('box_end') ) {
     function box_end() {
        echo '</div><!--- end of box border --></div></div></div></div></div></div></div></div></div>';
     }
}
if (!function_exists('shadow_start') ) {
     function shadow_start() {
        echo '<table align="center"><tr><td><div class="example" id="v6"><div id="main"><div class="wrap1"><div class="wrap2"><div class="wrap3" align="center">';
     }
}
if (!function_exists('shadow_end') ) {
     function shadow_end() {
        echo '</div></div></div></div></div></td></tr></table>';
     }
}
if (!function_exists('box_button') ) {
     function box_button($name,$image,$url,$description) {
?><div style="width:50%;height:80px;display:inline-table">
        <div class="boxbutton" id="<?=$name?>" >
            <a  href="<?=$url;?>" onclick="this.blur();new Effect.Pulsate('<?=$name?>',{ pulses: 1, duration: 0.5 });setTimeout('this.location=\'/<?=$url?>\'',1000);return false;">
                <img src="images/icons/32x32/<?=$image?>.png" align="left" />
                <b><?=$name?></b><br /><?=$description?>
            </a>
        </font>
        </div>
    </div><?
    }
}
?>
