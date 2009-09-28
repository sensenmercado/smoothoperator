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

    </head>
    <body>
        <div class="page_menu" style="width: 900px;background: #000;color: #fff;padding: 20px">

<?



    //box_start();
    for ($i = 0;$i < sizeof($menu_names);$i++) {
        echo '<a href="'.$menu_links[$i].'">'.$menu_names[$i].'</a> &nbsp;';
    }
    //echo $user_level;
    //box_end();
?>
        </div>

        <div class="content">