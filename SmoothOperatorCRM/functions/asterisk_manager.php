<?
if (!function_exists('agent_dial')) {
    function agent_dial($number) {
        global $config_values;
        return asterisk_make_call($config_values['manager_host'],
                           $config_values['manager_user'],
                           $config_values['manager_pass'],
                           "Agent/".$_SESSION['extension'],
                           "outbound",
                           $number,
                           "5551234",
                           "ClickToCall");
    }
}

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
            fputs($socket, "Secret: $pass\r\n");
            fputs($socket, "Events: off\r\n\r\n");
            do
            {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );

            fputs($socket, "Action: Originate\r\n");
            fputs($socket, "Async: True\r\n");
            fputs($socket, "Channel: $channel\r\n");
            fputs($socket, "Context: $context\r\n");
            fputs($socket, "Exten: $extension\r\n");
            fputs($socket, "Priority: 1\r\n");
            fputs($socket, "Callerid: $callerid\r\n");
            fputs($socket, "Account: $accountcode\r\n\r\n");
            do
            {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            fclose($socket);
        }
        return $wrets;
    }
}


/*


Action: QueuePause
[ActionID:] <value>
Interface: <value>
Paused: <value>
[Queue:] <value>
[Reason:] <value>
 */
if (!function_exists('asterisk_agent_change_status')) {
    function asterisk_agent_change_status($pause) {
        global $config_values;
        $timeout = 7500;
        $socket = fsockopen($config_values['manager_host'],"5038", $errno, $errstr, $timeout);
        if (!$socket) {
            echo 'Socket fail<br>';
            echo $errorno . '<br>';
            echo $errstr . '<br>';
            echo $timeout . '<br>';
        } else {
            fputs($socket, "Action: Login\r\n");
            fputs($socket, "UserName: ".$config_values['manager_user']."\r\n");
            fputs($socket, "Secret: ".$config_values['manager_pass']."\r\n");
            fputs($socket, "Events: off\r\n\r\n");
            do
            {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            
            fputs($socket, "Action: QueuePause\r\n");
            fputs($socket, "Interface: "."Agent/".$_SESSION['extension']."\r\n");
            fputs($socket, "Paused: $pause\r\n");
            do
            {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            fclose($socket);
        }
        return $wrets;
    }
}





?>
