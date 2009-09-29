<?
    /* Start or continue a logged in session */
    session_start();

    $current_directory = dirname(__FILE__);
    if (isset($override_directory)) {
            $current_directory = $override_directory;
    }

    require "functions/functions.php";

    $user_level = 0;
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id();
        $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
        $_SESSION['initiated'] = true;
    }


    if (isset($_SESSION['HTTP_USER_AGENT'])) {
        if (!($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))) {
            if (isset($_SESSION['user_name'])) {
                if (isset($_SESSION['user_level'])) {
                    $user_level = $_SESSION['user_level'];
                }
            }
        }
    }

    $full_path = $_SERVER['PHP_SELF'];
    $exploded_path = split("/",$full_path);
    $this_page = $exploded_path[sizeof($exploded_path)-1];
    
    //echo $this_page;
    if ($user_level > 0) {
    }
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
    require "config/db_config.php";
    
    $menu_items = get_menu_items($user_level);
    $undefined_links = get_undefined_links($user_level);

    $menu_names = $menu_items[0];
    $menu_links = $menu_items[1];

    $allowed = false;
    foreach($menu_links as $link) {
        //echo "Comparing "
        if ($this_page == $link) {
            $allowed = true;
        }
    }
    foreach($undefined_links as $link) {
        if ($this_page == $link) {
            $allowed = true;
        }
    }
    if (!$allowed) {
        header("Location: index.php");
        exit(0);
    }


    ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
                <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/default.css">

<script type="text/javascript" src="js/niftycube.js"></script>
<script type="text/javascript">
NiftyLoad=function(){
Nifty("ul#nav a","small transparent top");
}
</script>
    <style type="text/css">
html,body{margin:0;padding:0}
body{background: #FFF;
    font: 70% Arial,sans-serif}

div#header{font: 250% Arial,sans-serif;width: 90%;padding-top:20px;padding-left: 10%;background: #BBD9EE;text-align: left;color: #FFf8c6;}

div#menu{float:left;width: 100%;padding-top:20px;background: #BBD9EE}
ul#nav,ul#nav li{list-style-type:none;margin:0;padding:0}
ul#nav{margin-left: 20px;width:900px}

ul#nav li{float:left;margin-right: 3px;text-align: center}
ul#nav a{float:left;width: 10em;padding: 5px 0;background: #E7F1F8;text-decoration:none;color: #666}
ul#nav a:hover{background: #FFA826;color: #FFF}
ul#nav li.activelink a,ul#nav li.activelink a:hover{background: #FFF;color: #003}
</style>
    </head>
    <body>
        <center>
        

<?



    //box_start();
?>
<div id="header">
SmoothOperator CRM
</div>
<div id="menu">

    
    <ul id="nav"><?
 /*       <li id="home" class="activelink"><a href="#">Home</a></li>
        <li id="who"><a href="#">About</a></li>
        <li id="prod"><a href="#">Product</a></li>
        <li id="serv"><a href="#">Services</a></li>
        <li id="cont"><a href="#">Contact us</a></li>*/
    for ($i = 0;$i < sizeof($menu_names);$i++) {
        echo '<li id="'.$menu_names[$i].'" ';
        if ($this_page == $menu_links[$i]) {
            echo 'class="activelink"';
        }
        echo '><a href="'.$menu_links[$i].'" class="page_menu">'.$menu_names[$i].'</a></li>';
    }
?>    </ul>
</div><?
    //echo $user_level;
    //box_end();
?>
        
        <div class="content" align="center">