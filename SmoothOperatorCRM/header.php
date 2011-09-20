<?
/* Start or continue a logged in session */
session_start();

/* Find out the current location */
$current_directory = dirname(__FILE__);
if (isset($override_directory)) {
    $current_directory = $override_directory;
}

/* Include VentureVoIP Functions */
require "functions/functions.php";

/* Set user level to no access by default */
$user_level = 0;

/* Create a session if one has not already been created */
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
    $_SESSION['initiated'] = true;
}

/* Confirm that the user is using the same browser as they logged in with */
if (isset($_SESSION['HTTP_USER_AGENT'])) {
    if (!($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))) {
        if (isset($_SESSION['user_name'])) {
            if (isset($_SESSION['user_level'])) {
                $user_level = $_SESSION['user_level'];
            }
        }
    }
}

/* If the language is not set, set it to English - there is no current */
/* support for setting languages, but I realise it may be necessary in */
/* the future                                                          */
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = "en";
}

/* Get the actual PHP page (regardless of directory) */
$full_path = $_SERVER['PHP_SELF'];
$exploded_path = split("/",$full_path);
$this_page = $exploded_path[sizeof($exploded_path)-1];

/* If the user level is not set or if they have not logged in, and the */
/* current page is not login.php, send them to the login page.         */
if (!isset($user_level) || $user_level < 1) {
    if ($this_page != "login.php") {
        redirect("login.php");
        exit(0);
    }
}

/* Connect to the database */
require "config/db_config.php";

/* Get a list of menu items for this user level */
$menu_items = get_links($user_level, $connection, 1);
$menu_names = $menu_items[0];
$menu_links = $menu_items[1];
$menu_ids = $menu_items[2];

/* By default nobody is allowed to access anything */
$allowed = false;

/* If the page name is in the list of menu items for this user, allow it */
if (isset($menu_links)) {
    for($i = 0;$i<sizeof($menu_links);$i++) {
        if ($this_page == $menu_links[$i]) {
            $allowed = true;
            $this_page_id = $menu_ids[$i];
        }
    }
}

/* Get a list of pages that this user has access to but have no menu item */
$undefined_links_array = get_links($user_level, $connection, 0);

/* If the page name is in the list of non-menu items for this user, allow it */
for($i = 0; $i < sizeof($undefined_links_array[1]);$i++) {
    if ($this_page == $undefined_links_array[1][$i]) {
        $allowed = true;
        $this_page_id = $undefined_links_array[2][$i];
    }
}

unset($submenu_links_array);
$submenu_links_array = get_links($user_level, $connection, 1, -1, true);

/* If the page name is in the list of sub-menu items for this user, allow it */
for($i = 0; $i < sizeof($submenu_links_array[1]);$i++) {
    //foreach($submenu_links as $link) {
    if ($this_page == $submenu_links_array[1][$i]) {
        $allowed = true;
        $this_page_id = $submenu_links_array[2][$i];
    }
}

unset($undefined_links_array);

/* Get a list of pages that this user has access to but have no menu item */
$undefined_links_array = get_links($user_level, $connection, 0, 1);

/* If the page name is in the list of non-menu items for this user, allow it */
for($i = 0; $i < sizeof($undefined_links_array[1]);$i++) {
    if ($this_page == $undefined_links_array[1][$i]) {
        $allowed = true;
        $this_page_id = $undefined_links_array[2][$i];
    }
}

/* If we've reached here and still are not allowed, go to the index page */
if (!$allowed) {
    $_SESSION['messages'][] = "You have tried to access a page you are not permitted to access";
    header("Location: index.php");
    exit(0);
}

/* If we've made it this far, we're allowed to be viewing this page */

$config_values = $_SESSION['config_values'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?=stripslashes($config_values['site_name'])?></title>

<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/default.css">
<link rel="stylesheet" type="text/css" href="css/uploadify.css">

<script type="text/javascript" src="js/niftycube.js"></script>
<script type="text/javascript" src="js/prototype_1.6.1.js"> </script>
<script type="text/javascript" src="js/window.js"> </script>

<script type='text/javascript' src='js/jquery.min.1.2.6.js'></script>
<script type='text/javascript' src='js/jquery-ui.min.1.5.3.js'></script>
<script>
jQuery.noConflict();
</script>
<script type="text/javascript" src="js/jeip.js"> </script>


<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript" src="js/jquery.uploadify.v2.1.0.min.js"></script>

<!--  Add this to have a specific theme-->
<link href="themes/alphacube.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript">
function hide_message(layer_ref){
    jQuery.post("clear_message.php", {queryString: layer_ref});
    if(document.all){ //IS IE 4 or 5 (or 6 beta)
        eval("document.all." +layer_ref+ ".style.display = none");
    }
    if (document.layers) { //IS NETSCAPE 4 or below
        document.layers[layer_ref].display = 'none';
    }
    if (document.getElementById &&!document.all) {
        hza = document.getElementById(layer_ref);
        hza.style.display = 'none';
    }
}
NiftyLoad=function(){
    Nifty("div.thin_700px_box","large transparent");
    Nifty("div.box","large transparent");
    Nifty("thin_700px_box","large transparent");
    Nifty("box_med","large transparent");
    
    Nifty("input.rounded","large");
    Nifty("ul#nav a","small transparent top");
    <?
    if (isset($rounded)) {
        foreach ($rounded as $item) {
            ?>Nifty("<?=$item?>","large transparent");<?
        }
    }
    if (isset($_SESSION['messages'])) {
        ?>
        Nifty("div.messages","large transparent");
        <?
    }
    ?>
}
function show_confirm(in_text, in_delete_text, confirm_url) {
    Dialog.confirm(in_text,
                   {className: "alphacube", width:400, okLabel: in_delete_text,
                   buttonClass: "myButtonClass",
                   id: "myDialogId",
                   cancel:function(win) {;},
                   ok:function(win) {window.location=confirm_url;}
                   }
                   );
}
function showWindow2(in_title, in_text) {
    win = new Window({className: "mac_os_x", title: in_title, width:200, height:150, destroyOnClose: true, recenterAuto:false});
    win.getContent().update(in_text);
    win.showCenter();
}
</script>
<?
/* If a page set extra head before calling header.php, include the contents */
if (isset($extra_head)) {
    echo $extra_head;
}
?>
</head>
<body>
<center>
<?if ($this_page != "login.php") {?>
    <div id="header">
    <center>
    <b><?=stripslashes($config_values['site_name'])?></b>
    </center>
    </div>
    
    <div id="navigation">
    <ul>
    <?
    for ($i = 0;$i < sizeof($menu_names);$i++) {
        //$menu_link_page_name = explode("?", );
        if ($this_page == 'show_page.php') {
            $compare_to = $this_page."?".$_SERVER['QUERY_STRING'];
        } else {
            $compare_to = $this_page;
        }
        
        echo '<li ';
        if ($compare_to == $menu_links[$i]) {
            echo 'class="activelink"';
        }
        echo '><a href="'.$menu_links[$i].'" class="page_menu"><span>'.$menu_names[$i].'</span></a></li>';
    }
    //echo '<div style="display: inline-block;color: #fff"  id="date_div">('.$_SESSION['calls'].' calls)</div> <span style="display: inline-block;color: #fff"  id="job_details"></span>';
    ?>
    
    </ul>
    </div>
    <script type="text/javascript">
    function draw_date() {
        var currentTime = new Date();
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
        if (minutes < 10) {
            minutes = "0"+minutes;
        }
        if (seconds < 10) {
            seconds = "0"+seconds;
        }
        
        eval("document.all.date_div.innerHTML = '"+hours + ":" + minutes + ":"+seconds+" (<?=$_SESSION['calls']?> calls)'");
        
        //eval("document.all.date_div.innerHTML = ' (<?=$_SESSION['calls']?> calls)'");
        
        
        
        new Ajax.Request('check_calls.php?extension=<?=$_SESSION['extension']?>',
                         {
                         method:'get',
                         onSuccess: function(transport){
                         if (transport.responseText) {
                         var response = transport.responseText;
                         //alert("Success! \n\n" + response);
                         window.location = "get_customer.php?pop=1&phone_number="+response;
                         }
                         },
                         onFailure: function(){ alert('Something went wrong...') }
                         });
        
        new Ajax.Request('check_job.php?user_id=<?=$_SESSION['user_id']?>',
                         {
                         method:'get',
                         onSuccess: function(transport){
                         if (transport.responseText) {
                         var response = transport.responseText;
                         jQuery("#job_details").text("Job: "+response);
                         //alert("Success! \n\n" + response);
                         //window.location = "get_customer.php?pop=1&phone_number="+response;
                         }
                         },
                         onFailure: function(){ alert('Something went wrong...') }
                         });
        
        
        
        
        
        
    }
    setInterval(draw_date, 1000);
    </script>
    <?}
if ($this_page == "login.php") {?>
    
    <div id="content_login" align="center">
    <?} else {?>
        <div id="content" align="center">
        
        <?}


/* If we have any error messages, display them */
if (isset($_SESSION['messages'])) {
    foreach ($_SESSION['messages'] as $index=>$message) {?>
        <div class="messages" id = "message<?=$index?>" align="center" style="display: block;padding: 0px;">
        <a href="#" onclick="hide_message('message<?=$index?>')"><div class="messages" align="right" style="width: 99%;background: #fcc;margin:0px;">Close Message&nbsp;<img src="images/cross.png" border="0">&nbsp;</div></a>
        <?=$message.""?>
        </div>
        <?}
}

unset($links);
$links = get_links($user_level, $connection, 1, $this_page_id);
$link_names = $links[0];
$link_urls = $links[1];
$link_ids = $links[2];
$link_icons = $links[3];

if (sizeof($link_names) > 0) {
    ?>
    <div class="thin_700px_box">
    
    <?
    for ($i = 0;$i<sizeof($link_names);$i++) {
        echo '<a href="'.$link_urls[$i].'">';
        if (strlen($link_icons[$i]) > 0) {
            echo '<img src="images/'.$link_icons[$i].'" border="0">&nbsp;';
        }
        
        echo $link_names[$i].'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    ?>
    </div>
    <?
}
if ($this_page != "login.php") {
    ?>
<div id="site-bottom-bar" class="fixed-position">
<div id="site-bottom-bar-frame">
<div id="site-bottom-bar-content">
<?
echo '<div style="display: inline-block;color: #999"  id="date_div">'.@Date("H:i:s").' ('.$_SESSION['calls'].' calls)</div> <span style="display: inline-block;color: #999"  id="job_details">Job: ';
include "check_job.php";
echo '</span>';
echo '&nbsp;<span id="status_bar" style="display: inline-block;color: #f00;font-weight: bold;"></span>';
?>
</div>
</div>
</div>
<?
}?>