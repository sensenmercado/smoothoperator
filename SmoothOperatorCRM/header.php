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
    $menu_items = get_menu_items($user_level);
    $menu_names = $menu_items[0];
    $menu_links = $menu_items[1];

    /* Get a list of pages that this user has access to but have no menu item */
    $undefined_links = get_undefined_links($user_level);

    /* By default nobody is allowed to access anything */
    $allowed = false;

    /* If the page name is in the list of menu items for this user, allow it */
    foreach($menu_links as $link) {
        if ($this_page == $link) {
            $allowed = true;
        }
    }

    /* If the page name is in the list of non-menu items for this user, allow it */
    foreach($undefined_links as $link) {
        if ($this_page == $link) {
            $allowed = true;
        }
    }

    /* If we've reached here and still are not allowed, go to the index page */
    if (!$allowed) {
        $messages[] = "You have tried to access a page you are not permitted to access";
        $_SESSION['messages'] = $messages;
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
        <script type="text/javascript" src="js/niftycube.js"></script>
        <script type="text/javascript" src="js/prototype_1.6.1.js"> </script>
<script type="text/javascript" src="js/window.js"> </script>

<!--  Add this to have a specific theme-->
<link href="themes/alphacube.css" rel="stylesheet" type="text/css"/>

        <script type="text/javascript">
            NiftyLoad=function(){
                Nifty("ul#nav a","small transparent top");
                <?
                if (isset($_SESSION['messages'])) {
                    ?>
                    Nifty("div#messages","large transparent");
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
            <div id="header">
                <div id="header2">
                    <b><?=$config_values['site_name']?></b>
                </div>
            </div>
            <div id="menu">                
                <ul id="nav">
                    <?
                    for ($i = 0;$i < sizeof($menu_names);$i++) {
                        echo '<li id="'.$menu_names[$i].'" ';
                        if ($this_page == $menu_links[$i]) {
                            echo 'class="activelink"';
                        }
                        echo '><a href="'.$menu_links[$i].'" class="page_menu">'.$menu_names[$i].'</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <?
            /* If we have any error messages, display then remove them */
            if (isset($_SESSION['messages'])) {
            ?>
                <div id="messages" align="center">
                    <br /><b>Messages:</b><br /><br />
                    <?foreach ($_SESSION['messages'] as $message) {
                        echo $message."<br /><br />";
                    }
                    unset($_SESSION['messages']);?>
                </div>
            <?}?>
            <div id="content" align="center">