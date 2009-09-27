<?
require $current_directory."/functions/read_config_file.php";

require $current_directory."/functions/bitmask.php";

require $current_directory."/functions/authentication.php";

require $current_directory."/functions/sanitize.php";

require $current_directory."/functions/urls.php";

require $current_directory."/functions/layout.php";

require $current_directory."/functions/database.php";

if (!function_exists('sec2hms') ) {
  function sec2hms ($sec, $padHours = false) {

    // holds formatted string
    $hms = "";

    // there are 3600 seconds in an hour, so if we
    // divide total seconds by 3600 and throw away
    // the remainder, we've got the number of hours
    $hours = intval(intval($sec) / 3600);

    // add to $hms, with a leading 0 if asked for
    $hms .= ($padHours)
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
          : $hours. ':';

    // dividing the total seconds by 60 will give us
    // the number of minutes, but we're interested in
    // minutes past the hour: to get that, we need to
    // divide by 60 again and keep the remainder
    $minutes = intval(($sec / 60) % 60);

    // then add to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

    // seconds are simple - just divide the total
    // seconds by 60 and keep the remainder
    $seconds = intval($sec % 60);

    // add to $hms, again with a leading 0 if needed
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    // done!
    return $hms;

  }
}
if (!function_exists('extractXML') ) {
    function extractXML($xml) {

        if (!($xml->children())) {
            return (string) $xml;
        }

        foreach ($xml->children() as $child) {
            $name=$child->getName();
            if (count($xml->$name)==1) {
                $element[$name] = extractXML($child);
            } else {
                $element[][$name] = extractXML($child);
            }
        }

        return $element;
    }
}
if (!function_exists('print_pre') ) {
    function print_pre($text) {
        echo "<pre>";
        print_r( $text);
        echo "</pre>";
    }
}

if (!function_exists('draw_icons') ) {
    function draw_icons($section) {
        if ($handle = opendir('./modules')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $xml = simplexml_load_file("./modules/".$file);
                    if (isset($xml->icon)) {
                        $icon = $xml->icon;
                    } else {
                        $icon = "application";
                    }
                    if ($xml->menu->section == $section) {
                        box_button($xml->name, $icon,$xml->menu->link,$xml->description);
                    }
                }
            }
        }
    }
}


if (!function_exists('check_for_gd_library') ) {
    function check_for_gd_library() {
        if (!extension_loaded('gd')) {
            echo "It looks like php-gd is not installed.  Installing it will depend ";
            echo "on the package manager you have installed.  Here are a few examples:<br /><br />";
            echo "Fedora/Centos:<br /><code>yum install -y php-gd</code><br /><br />";
            echo "Debian/Ubuntu:<br /><code>apt-get install php-gd</code><br /><br />";
            echo "Gentoo:<br /><code>emerge php-gd</code><br /><br />";
            echo "Mandriva/Mandrake:<br /><code>urpmi php-gd</code><br /><br />";
            exit(0);
        }

    }
}

if (!function_exists('check_for_upload_settings') ) {
    function check_for_upload_settings($current_directory) {
        if (!file_exists($current_directory."/../upload_settings.inc")) {
            if (!file_exists($current_directory."/../../upload_settings.inc")) {
                echo "The file ../upload_settings.inc does not exist.  You will need to ";
                echo "copy it from the $current_directory/cron subdirectory by typing ";
                echo "the following commands<br /><br />";
                echo "<code>cp $current_directory/cron/upload_settings.inc $current_directory/../</code>";
                exit(0);
            }
        }
    }
}

if (!function_exists('check_for_upload_directory') ) {
    function check_for_upload_directory($whoami) {
        if (!file_exists("/var/tmp/uploads")) {
	    $current_directory = dirname(__FILE__)."/../";
            echo "The directory /var/tmp/uploads does not exist.  You will need to create ";
            echo "it by typing the following commands<br /><br />";
            echo "<code>mkdir /var/tmp/uploads<br />";
            echo "chown $whoami /var/tmp/uploads<br />";
            echo "cp $current_directory/uploads/* /var/tmp/uploads</code>";
            exit(0);
        }

    }
}
if (!function_exists('get_backend_version') ) {
    function get_backend_version() {
        if (file_exists("/SmoothTorque/SmoothTorque.version")) {
    	    $fp2 = fopen("/SmoothTorque/SmoothTorque.version", "r");
    	    while (!feof($fp2)) {
    	    	$line = trim(fgets($fp2));
    	    	if (strlen($line)>0){
    	    		$version = substr($line,0,strlen($line)-1);
    	    	}
    	    }
    	    fclose ($fp2);
            if($version>0){
                $version/=100;
            }
        }
        return $version;
    }
}

if (!function_exists('_get_browser') ) {
    function _get_browser() {
        $browser = array ( //reversed array
          "OPERA",
          "MSIE",            // parent
          "NETSCAPE",
          "FIREFOX",
          "SAFARI",
          "KONQUEROR",
          "MOZILLA"        // parent
        );

        $info[browser] = "OTHER";

        foreach ($browser as $parent) {
            if ( ($s = strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), $parent)) !== FALSE ) {
                $f = $s + strlen($parent);
                $version = substr($_SERVER['HTTP_USER_AGENT'], $f, 5);
                $version = preg_replace('/[^0-9,.]/','',$version);
                $info[browser] = $parent;
                $info[version] = $version;
                break; // first match wins
            }
        }
        return $info;
    }
}

if (!function_exists('get_menu_html') ) {
function get_menu_html($config_values, $self, $level) {
    $menu='<CENTER>
    <table border="0" cellpadding="3" cellspacing="0"><TR HEIGHT="10">';
    //=======================================================================================================
    // Home
    //=======================================================================================================
    if ($self=="/main.php"){
        $menu.='<td style="background-image: url(/images/clb.gif);"></td>';
        $thead="thead";
    } else {
        $menu.='<TD CLASS="theadl2" WIDTH=0></TD>';
        $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
    }

    $menu.='<TD class="'.$thead.'" height=27><A HREF="/main.php"><img width="16" height="16" src="/images/house.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_HOME']).'</A>&nbsp;</TD>';

    if ($level==sha1("level100")||$level==sha1("level0")){
    //=======================================================================================================
    // Campaigns
    //=======================================================================================================
    if ($self=="/campaigns.php"||$self=="/report.php"||$self=="/resetlist.php"||$self=="/list.php"||$self=="/deletecampaign.php"||$self=="/editcampaign.php"||$self=="/addcampaign.php"||$self=="/stopcampaign.php"||$self=="/startcampaign.php"||$self=="/test.php"
    ){
        $thead="thead";
    } else {
        $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
    }
    $menu.='<TD class="'.$thead.'"><A HREF="/campaigns.php"><img width="16" height="16"  src="/images/folder.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_CAMPAIGNS']).'</A>&nbsp;</TD>';

    //=======================================================================================================
    // Numbers
    //=======================================================================================================
    if ($self=="/addnumbers.php"||$self=="/serverlist.php"||$self=="/numbers.php"||$self=="/deletenumber.php"||$self=="/viewnumbers.php"||$self == "/gennumbers.php"||$self == "/upload.php"||$self =="//receive.php"||$self=="/resetnumber.php"){
        $thead="thead";
    } else {
        $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
    }
    $menu.='<TD class="'.$thead.'"><A HREF="/numbers.php"><img width="16" height="16"  src="/images/telephone.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_NUMBERS']).'</A>&nbsp;</TD>';

    //=======================================================================================================
    // DNC Numbers
    //=======================================================================================================
    if ($self=="/adddncnumbers.php"||$self=="/dncnumbers.php"||$self=="/deletedncnumber.php"||$self=="/viewdncnumbers.php"||$self == "/gendncnumbers.php"||$self == "/uploaddnc.php"||$self =="//receivednc.php"){
        $thead="thead";
    } else {
        $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
    }
    $menu.='<TD class="'.$thead.'"><A HREF="/dncnumbers.php"><img width="16" height="16"  src="/images/telephone_error.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_DNC']).'</A>&nbsp;</TD>';

    //=======================================================================================================
    // Messages
    //=======================================================================================================
    if ($self=="/editmessage.php"||$self=="/addmessage.php"||$self=="/deleteMessage.php"||$self=="/messages.php"||$self=="/uploadmessage.php"){
        $thead="thead";
    } else {
        $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
    }
    $menu.='<TD class="'.$thead.'"><A HREF="/messages.php"><img width="16" height="16"  src="/images/sound.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_MESSAGES']).'</A>&nbsp;</TD>';
    //=======================================================================================================
    // Schedules
    //=======================================================================================================
    if ($self=="/editschedule.php"||$self=="/addschedule.php"||$self=="/deleteschedule.php"||$self=="/schedule.php"){
        $thead="thead";
    } else {
        $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
    }
    $menu.='<TD class="'.$thead.'"><A HREF="/schedule.php"><img width="16" height="16"  src="/images/clock.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_SCHEDULES']).'</A>&nbsp;</TD>';
    if ($level==sha1("level100")){

        //=======================================================================================================
        // Customers
        //=======================================================================================================
        if ($self=="/deletecustomer.php"||$self=="/addcustomer.php"||$self=="/customers.php"||$self=="/editcustomer.php"){
            $thead="thead";
        } else {
            $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
        }
        $menu.='<TD class="'.$thead.'"><A HREF="/customers.php"><img width="16" height="16"  src="/images/group.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_CUSTOMERS']).'</A>&nbsp;</TD>';
        //=======================================================================================================

        //=======================================================================================================
        // Queues
        //=======================================================================================================
        if ($self=="/deletequeue.php"||$self=="/addqueue.php"||$self=="/queues.php"||$self=="/editqueue.php"){
            $thead="thead";
        } else {
            $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
        }
        $menu.='<TD class="'.$thead.'"><A HREF="/queues.php"><img width="16" height="16"  src="/images/database.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_QUEUES']).'</A>&nbsp;</TD>';
        //=======================================================================================================

        //=======================================================================================================
        // Servers
        //=======================================================================================================
        if ($self=="/deleteserver.php"||$self=="/addserver.php"||$self=="/servers.php"||$self=="/editserver.php"){
            $thead="thead";
        } else {
            $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
        }

        $menu.='<TD class="'.$thead.'"><A HREF="/servers.php"><img width="16" height="16"  src="/images/server.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_SERVERS']).'</A>&nbsp;</TD>';
        //=======================================================================================================


        //=======================================================================================================
        // Trunks
        //=======================================================================================================
        if ($self=="/trunks.php"||$self=="/edittrunk.php"||$self=="/addtrunk.php"||$self=="/setdefault.php"||$self=="/deletetrunk.php"){
            $thead="thead";
        } else {
            $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
        }
        $menu.='<TD class="'.$thead.'"><A HREF="/trunks.php"><img width="16" height="16"  src="/images/telephone_link.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_TRUNKS']).'</A>&nbsp;</TD>';
        //=======================================================================================================

        //=======================================================================================================
        // Admin
        //=======================================================================================================
        if ($self=="/config.php"||$self=="/setparameter.php"){
            $thead="thead";
        } else {
            $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
        }
        $menu.='<TD class="'.$thead.'"><A HREF="/config.php"><img width="16" height="16"  src="/images/cog.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_ADMIN']).'</A>&nbsp;</TD>';
        //=======================================================================================================

    }
    //    <TD class="thead2"><A HREF="prefs.php">Preferences</A>&nbsp;&nbsp;</TD>
    } else if ($level==sha1("level10")){
        /*//=======================================================================================================
        // Customers
        //=======================================================================================================
        if ($self=="/deletecustomer.php"||$self=="/addcustomer.php"||$self=="/customers.php"||$self=="/editcustomer.php"){
            $thead="thead";
        } else {
            $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
        }
        $menu.='<TD class="'.$thead.'"><A HREF="/customers.php"><img width="16" height="16"  src="/images/group.png" border="0" align="left">'.$config_values['MENU_CUSTOMERS'].'</A>&nbsp;</TD>';
        //=======================================================================================================
        */

        //echo "Billing Administrator Login";
        // This is for people who are logged in as a billing administrator
        //=======================================================================================================
        // Add Funds
        //=======================================================================================================
        if ($self=="/addfunds.php"){
            $thead="thead";
        } else {
            $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";
        }
        $menu.='<TD class="'.$thead.'"><A HREF="/addfunds.php"><img width="16" height="16"  src="/images/group.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_ADDFUNDS']).'</A>&nbsp;</TD>';
        //=======================================================================================================

    }
    $thead="thead2\" onmouseover=\"this.className='thead'\" onmouseout=\"this.className='thead2'\"  \"";

    $menu.='<TD height="1" class="'.$thead.'"><A HREF="/logout.php"><img width="16" height="16"  src="/images/door_in.png" border="0" align="left">'.str_replace(" ","&nbsp;",$config_values['MENU_LOGOUT']).'</A>&nbsp;</TD><TD CLASS="theadr2" WIDTH=0></TD></TR></table>

    ';
    return $menu;
}
}
?>
