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
 Action: Redirect
 Channel: SIP/302-0a3453a0
 ExtraChannel: SIP/301-b6189ab8
 Exten: 501
 Context: from-internal
 Priority: 1
 */

if (!function_exists('transfer_to_extension')) {
    function transfer_to_extension($channel1, $channel2, $context, $extension, $priority) {
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
            do {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            
            fputs($socket, "Action: Redirect\r\n");
            fputs($socket, "Channel: $channel1\r\n");
            fputs($socket, "ExtraChannel: $channel2\r\n");

            fputs($socket, "Context: $context\r\n");
            fputs($socket, "ExtraContext: $context\r\n");
            
            fputs($socket, "Exten: $extension\r\n");
            fputs($socket, "ExtraExten: ".$extension."1\r\n");
            
            fputs($socket, "Priority: $priority\r\n");
            fputs($socket, "ExtraPriority: $priority\r\n\r\n");
            do {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            fclose($socket);
        }
        return $wrets;
    }
}

if (!function_exists('transfer_single_to_extension')) {
    function transfer_single_to_extension($channel1, $context, $extension, $priority) {
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
            do {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            
            fputs($socket, "Action: Redirect\r\n");
            fputs($socket, "Channel: $channel1\r\n");
            
            fputs($socket, "Context: $context\r\n");
            
            fputs($socket, "Exten: $extension\r\n");
            
            fputs($socket, "Priority: $priority\r\n\r\n");
            do {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            fclose($socket);
        }
        return $wrets;
    }
}


if (!function_exists('at_xfer')) {
    function at_xfer($channel, $context, $exten, $priority) {
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
            do {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            
            fputs($socket, "Action: AtXfer\r\n");
            fputs($socket, "Channel: $channel\r\n");
            fputs($socket, "Context: $context\r\n");
            fputs($socket, "Exten: $exten\r\n");
            fputs($socket, "Priority: $priority\r\n\r\n");
            do {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            fclose($socket);
        }
        return $wrets;
    }
}

if (!function_exists('park_call')) {
    function park_call($channel1, $channel2, $slot,$timeout_park) {
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
            do {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            
            fputs($socket, "Action: Park\r\n");
            fputs($socket, "Channel: $channel1\r\n");
            fputs($socket, "Channel2: $channel2\r\n");
            
            fputs($socket, "Parkinglot: $slot\r\n");
            fputs($socket, "Timeout: $timeout_park\r\n\r\n");
            do {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            fclose($socket);
        }
        return $wrets;
    }
}

if (!function_exists('bridge')) {
    function bridge($channel1, $channel2) {
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
            do {
                $line = fgets($socket, 4096);
                $wrets .= $line;
                $info = stream_get_meta_data($socket);
            } while ($line != "\r\n" && $info['timed_out'] == false );
            
            fputs($socket, "Action: Bridge\r\n");
            fputs($socket, "Channel1: $channel1\r\n");
            fputs($socket, "Channel2: $channel2\r\n\r\n");
            do {
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
            fputs($socket, "Paused: $pause\r\n\r\n");
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
