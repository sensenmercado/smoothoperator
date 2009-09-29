<?
if (!function_exists('get_undefined_links')) {
    function get_undefined_links($user_level) {
        $retval[] = 'login.php';
        if ($user_level > 0) {
            $retval[] = 'get_customer.php';
        }
        return $retval;
    }
}

if (!function_exists('clean_field_name')) {
    function clean_field_name($field) {
        return ucwords(strtolower(str_replace("_", " ",$field)));
    }
}

if (!function_exists('get_menu_items') ) {
    function get_menu_items ($user_level) {
        if ($user_level == 0) {
            $menu_names[] = "Login";
            $menu_links[] = "login.php";
        }
        if ($user_level > 0) { // Normal User
            $menu_names[] = "Home";
            $menu_links[] = "index.php";
            $menu_names[] = "Search Customer";
            $menu_links[] = "search_customer.php";
        }
        if ($user_level > 9) { // Admin
            $menu_names[] = "Customers";
            $menu_links[] = "list_customers.php";

            $menu_names[] = "Manage Lists";
            $menu_links[] = "manage_lists.php";

            }
        if ($user_level > 99) { // Super User
            $menu_names[] = "System Settings";
            $menu_links[] = "config.php";

        }
        if ($user_level > 0) {
            $menu_names[] = "Logout";
            $menu_links[] = "logout.php";
        }
        $retval[0] = $menu_names;
        $retval[1] = $menu_links;
        return $retval;
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
                <img src="/images/64x64/<?=$image?>.png" align="left" />
                <b><?=$name?></b><br /><?=$description?>
            </a>
        </font>
        </div>
    </div><?
    }
}
?>
