<?
    /* Start or continue a logged in session */
    session_start();
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
<?
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
            }
            break;
    }

    draw_menu($user_level);
    
?>
