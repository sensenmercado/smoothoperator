<?
if (!function_exists('asterisk_make_call')) {
    function asterisk_make_call($host, $user, $pass, $channel, $context, $extension, $callerid="Unknown", $accountcode="Unknown") {
        $timeout = 7500;
        $socket = fsockopen($host,"5038", $errno, $errstr, $timeout);
        if (!$socket) {
          echo 'Socket fail<br>';
          echo $errorno . '<br>';
          echo $errstr . '<br>';
          echo $timeout . '<br>';
        } else {
            fputs($socket, "Action: Login\r\n");
            fputs($socket, "UserName: $user\r\n");
            fputs($socket, "Secret: $pass\r\n\r\n");
            fputs($socket, "Action: Originate\r\n");
            fputs($socket, "Channel: $channel\r\n");
            fputs($socket, "Context: $context\r\n");
            fputs($socket, "Exten: $extension\r\n");
            fputs($socket, "Priority: 1\r\n");
            fputs($socket, "Callerid: $callerid\r\n");
            fputs($socket, "Account: $accountcode\r\n\r\n");
            $wrets=fgets($socket);
            $wrets=fgets($socket);
            $wrets=fgets($socket);
            fclose($socket);
        }
    }
}
?>
