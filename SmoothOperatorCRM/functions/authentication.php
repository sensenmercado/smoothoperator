<?
if (!function_exists('is_authenticated') ) {
    function is_authenticated () {
        $user=$_COOKIE[user_wp];
//        echo "Cookie Logged In: $_COOKIE[loggedin_wp]<br />";
//        echo "Should be Logged In: $_COOKIE[loggedin_wp]<br />";
        return $_COOKIE["loggedin_wp"]==sha1("LoggedIn".$user);
        //return true;
    }
}

?>
