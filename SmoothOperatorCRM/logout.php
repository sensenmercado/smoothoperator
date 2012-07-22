<?php
session_start();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Finally, destroy the session.
session_destroy();
    ?>
<script>
    top.location.href = "index.php" ;
</script>
<?
//header("location: index.php");
?>
