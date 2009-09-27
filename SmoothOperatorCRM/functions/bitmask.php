<?
$ROUTING = 1;
$QUEUE_STATS = 2;
$QUEUES = 4;
$STAFF = 8;
$OPERATOR_PANEL = 16;
$CALL_RECORDS = 32;
$USERS = 64;
$SUPPORT = 128;
$TELESALES_STATS = 256;
$RECEPTION = 512;

if (!function_exists('safeBitCheck') ) {

function safeBitCheck($number,$comparison) {
    if( $number < 2147483647 ) {
        return ($number & $comparison)==$comparison;
    } else {
        $binNumber = strrev(base_convert($number,10,2));
        $binComparison = strrev(base_convert($comparison,10,2));
        for( $i=0; $i<strlen($binComparison); $i++ ) {
            if( strlen($binNumber) - 1 <$i || ($binComparison{$i}==="1" && $binNumber{$i}==="0") ) {
                return false;
            }
        }
        return true;
    }
}

}
?>
