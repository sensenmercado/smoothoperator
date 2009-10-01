<?
$queryString = substr($_POST['queryString'],7);
session_start();
foreach($_SESSION['messages'] as $index=>$message) {
    if ($index == $queryString) {
        
        unset($_SESSION['messages'][$index]);
    }
}
?>
