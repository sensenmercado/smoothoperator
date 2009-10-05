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

    if (!isset($_SESSION['language'])) {
        $_SESSION['language'] = "en_gb";
    }

    /* Get the actual PHP page (regardless of directory) */
    $full_path = $_SERVER['PHP_SELF'];
    $exploded_path = split("/",$full_path);
    $this_page = $exploded_path[sizeof($exploded_path)-1];
    
    switch ($user_level) {
        case 1:   // Normal User
            break;
        case 10:  // Administrator
            break;
        case 100: // Super User
            break;
        default:  // Not logged in
            // If not on login page, send to it
            if ($this_page != "login.php") {
                header("Location: login.php");
                exit(0);
            }
            break;
    }

    /* Connect to the database */
    require "config/db_config.php";

    /* Get a list of menu items for this user level */
    $menu_items = get_links($user_level, $connection, 1);
    $menu_names = $menu_items[0];
    $menu_links = $menu_items[1];


    /* By default nobody is allowed to access anything */
    $allowed = false;

    /* If the page name is in the list of menu items for this user, allow it */
    if (isset($menu_links)) {
        foreach($menu_links as $link) {
            if ($this_page == $link) {
                $allowed = true;
            }
        }
    }

    /* Get a list of pages that this user has access to but have no menu item */
    $undefined_links_array = get_links($user_level, $connection, 0, 0);
    $undefined_links = $undefined_links_array[1];

    /* If the page name is in the list of non-menu items for this user, allow it */
    foreach($undefined_links as $link) {
        if ($this_page == $link) {
            $allowed = true;
        }
    }

    unset($undefined_links_array);

    /* Get a list of pages that this user has access to but have no menu item */
    $undefined_links_array = get_links($user_level, $connection, 0, 1);
    $undefined_links = $undefined_links_array[1];

    /* If the page name is in the list of non-menu items for this user, allow it */
    foreach($undefined_links as $link) {
        if ($this_page == $link) {
            $allowed = true;
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
    //print_pre($config_values);
    ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?=$config_values['site_name']?></title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/default.css">
        <link rel="stylesheet" type="text/css" href="css/uploadify.css">

        <script type="text/javascript" src="js/niftycube.js"></script>
        <script type="text/javascript" src="js/prototype_1.6.1.js"> </script>
        <script type="text/javascript" src="js/window.js"> </script>
        <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js'></script>
        <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.5.3/jquery-ui.min.js'></script>
        <script type="text/javascript" src="js/swfobject.js"></script>
        <script type="text/javascript" src="js/jquery.uploadify.v2.1.0.min.js"></script>

<?
if (isset($extra_head)) {
    echo $extra_head;
}
?>
<style type='text/css'>


</style>
<!--  Add this to have a specific theme-->
<link href="themes/alphacube.css" rel="stylesheet" type="text/css"/>

        <script type="text/javascript">
function hide_message(layer_ref){
    $.post("clear_message.php", {queryString: layer_ref});
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
                Nifty("div.xxxx","large transparent");
                
                Nifty("input.rounded","large");
                Nifty("ul#nav a","small transparent top");
                <?
                if (isset($rounded)) {
                    foreach ($rounded as $item) {
                      ?>
                    Nifty("<?=$item?>","large transparent");
                    <?
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
              });
            }
function showWindow2(in_title, in_text) {
win = new Window({className: "mac_os_x", title: in_title, width:200, height:150, destroyOnClose: true, recenterAuto:false});

win.getContent().update(in_text);
win.showCenter();
   }

</script>





    </head>
    <body>
        <center>
        <?if ($this_page != "login.php") {?>
            <div id="header">
                <div id="header2" align="center">
                <center>

                    <b><?=$config_values['site_name']?></b>
                    </center>
                </div>
            </div>
            
            <div id="menu">
                <ul id="nav">
                    <?
                    for ($i = 0;$i < sizeof($menu_names);$i++) {
                        //$menu_link_page_name = explode("?", );
                        if ($this_page == 'show_page.php') {
                            $compare_to = $this_page."?".$_SERVER['QUERY_STRING'];
                        } else {
                            $compare_to = $this_page;
                        }

                        echo '<li id="'.$menu_names[$i].'" ';
                        if ($compare_to == $menu_links[$i]) {
                            echo 'class="activelink"';
                        }
                        echo '><a href="'.$menu_links[$i].'" class="page_menu">'.$menu_names[$i].'</a></li>';
                    }
                    ?>
                </ul>
            </div><?}
            if ($this_page == "login.php") {?>
                
            <div id="content_login" align="center">
            <?} else {?>
            <div id="content" align="center">

            <?}


            /* If we have any error messages, display them */
            if (isset($_SESSION['messages'])) {
            ?>
            <?foreach ($_SESSION['messages'] as $index=>$message) {?>
                <div class="messages" id = "message<?=$index?>" align="center" style="display: block;padding: 0px;">
                    <a href="#" onclick="hide_message('message<?=$index?>')"><div class="messages" align="right" style="width: 99%;background: #fcc;margin:0px;">Close Message&nbsp;<img src="images/cross.png" border="0">&nbsp;</div></a>
                    
                    <?
                        echo $message."";
                        //unset($_SESSION['messages']);
                    ?>

                </div>
            <?}}?>
    