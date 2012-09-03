<?
$manager_hostname = $argv[1];
$manager_username = $argv[2];
$manager_password = $argv[3];
$manager_port = "5038";
$connection_timeout = 3;
echo "Starting up with hostname: $argv[1] username: $argv[2] password: $argv[3]\n";

/*
 Event: Link
 Privilege: call,all
 Channel1: SIP/agent_0002-0000000d
 Channel2: Agent/0002
 Uniqueid1: 1326245635.14
 Uniqueid2: 1326245635.15
 CallerID1: agent_0002
 CallerID2: agent_0002
 */
require "../config/db_config.php";

$MYSQL_BACKEND = true;
$FILE_BACKEND = false;
$DEBUG_MANAGER = false;
$DEBUG_UNKNOWN = true;
require "manager_events.php";

mysqli_query($connection, "DELETE FROM SmoothOperator.channels");

while (1) {
    $event = false;
    // Connect to the Asterisk Manager and log in
    $socket = fsockopen($manager_hostname, $manager_port, $errno, $errstr, $connection_timeout);
    fputs($socket, "Action: Login\r\n");
    fputs($socket, "UserName: $manager_username\r\n");
    fputs($socket, "Secret: $manager_password\r\n\r\n");
    echo chr(27)."[H".chr(27)."[2J";
    
    echo "+===================================+\n";
    echo "|   \033[1m\033[33mPHP Asterisk Manager Interface\033[0m\033[22m  |\n";
    echo "|   \033[1m\033[33m Written by Matt Riddell 2012\033[0m\033[22m   |\n";
    echo "+===================================+\n";
    echo "|   \033[1m\033[32mConnected to Asterisk Manager\033[0m\033[22m   |\n";
    echo "+===================================+\n";
    echo "\n";
    
    // Continue while it is still connected
    $x = 0;
    while (!feof($socket)) {
        $x++;
        fputs($socket, "Action: Status\r\n\r\n");
        fputs($socket, "Action: CoreShowChannels\r\n\r\n");
        //echo ".";
        sleep(1);
        unset ($resp);
        unset($wrets);
        /* This is slightly complicated.  Because the Asterisk Manager sometimes sends
         * one event across packets or multiple events in one packet, we need to buffer
         * up the data we receive until the last 4 characters are equal to \r\n\r\n
         * This signifies the end of an event or piece of information.
         */
        $wrets = "";
        while (!(strlen($wrets) >= 4 && substr($wrets, strlen($wrets)-4) == "\r\n\r\n") ){
            if (!($wrets .= fread($socket, 8192))) {
                // Socket error - reconnect
                echo "-";
                while (!($socket = fsockopen($manager_hostname, $manager_port, $errno, $errstr, $connection_timeout))) {
                    sleep(3);
                    echo "Sleeping waiting for reconnect\n";
                }
                echo "Reconnected successfully\n";
                fputs($socket, "Action: Login\r\n");
                fputs($socket, "UserName: $manager_username\r\n");
                fputs($socket, "Secret: $manager_password\r\n\r\n");
            }
        }
        if ($DEBUG_MANAGER) {
            echo "Received: ".$wrets."\n";
        }
        //$wrets now contains a potential event
        $contents = $wrets;
        //$contents = str_replace("\r", "", $contents);
        $lines = explode("\r\n", $contents);
        foreach ($lines as $line) {
            // Remove any whitespace at the ends of the lines
            $line = trim($line);
            // Remove the \r - the \n was removed above
            $line = str_replace("\n", "", $line);
            
            if (substr($line, 0, 6) == "Event:") {
                /* Event found - trim off the word "Event: " */
                $line = trim(substr($line, 7));
                /* The remainder is the name of the event */
                $eventname = $line;
                /* Anything we received could now be part of an event */
                $event = true;
            } else {
                if (!$event) { /* not in an event */
                    if (strlen($line) != 0) {
                        // received a line which was not part of an event
                        if ($line == "Message: Authentication accepted") {
                            $logged_in = true;
                        } else if ($line == "Asterisk Call Manager/1.0") {
                        } else if ($line == "Response: Success") {
                        } else if ($line == "Response: Error") {
                        } else if ($line == "Message: Authentication failed") {
                            $logged_in = false;
                            echo "| Authentication with Asterisk      |\n";
                            echo "| Manager connection failed.        |\n";
                            echo "| Check your username and password. |\n";
                            echo "+===================================+\n";
                            echo "| Program now exiting.              |\n";
                            echo "+===================================+\n";
                            exit (0);
                        } else {
                            if ($DEBUG_MANAGER) {
                                echo "===] Non Event Line: $line [===\n";
                            }
                        }
                    }
                } else { 
                    /* in an event - name is $eventname */
                    /* if we get a blank line then we have gotten to the */
                    /* end of this event - do something with it.         */
                    if (strlen($line) == 0) {
                        $event = false;
                        if ($eventname == "PeerStatus") {
                            peer_status($peer_name, $peer_status, $cause, $time, $cause_txt);
                        } else if ($eventname == "UserEvent") {
                            /* If we get a user event we need to make this available to the */
                            /* frontend interface.  It is either a disconnect or a connect. */
                            if ( isset ($remote_agent_disconnect)) {
                                //list($agent_num, $cidname, $cidnum) = explode("-",$remote_agent);
                                agent_disconnected($remote_agent_disconnect);
                            } else {
                                list ($agent_num, $queue_name, $callerid) = explode("-", $remote_agent);
                                agent_connected($agent_num, $queue_name, $callerid);
                            }
                        } else if ($eventname == "QueueMemberStatus") {
                            queue_member_status($member_name, $queue, $location, $membership, $calls_taken, $last_call, $status, $paused, $penalty);
                        } else if ($eventname == "Link") {
                            asterisk_link($chan_1, $chan_2, $clid_1, $clid_2, "Link");
                        } else if ($eventname == "Bridge") {
                            asterisk_link($chan_1, $chan_2, $clid_1, $clid_2, "Bridge", $bridgetype);
                        } else if ($eventname == "Unlink") {
                            asterisk_unlink($chan_1, $chan_2, $clid_1, $clid_2);
                        } else if ($eventname == "Newstate") {
                            $exploded = explode("-", $channel);
                            $peer_name = trim($exploded[0]);
                            if (!isset($state)) {
                                $state = "";
                            }
                            if (!isset($callerid)) {
                                $callerid = "";
                            }
                            if (!isset($callerid_name)) {
                                $callerid_name = "";
                            }
                            if (!isset($unique_id)) {
                                $unique_id = "";
                            }
                            new_state($peer_name, $state, $callerid, $callerid_name, $unique_id);
                        } else if ($eventname == "Newchannel") {
                        } else if ($eventname == "Newcallerid") {
                        } else if ($eventname == "Join") {
                        } else if ($eventname == "StatusComplete") {
                        } else if ($eventname == "Status") {
                            if (!isset($calleridname)) {
                                $calleridname = "";
                            }
                            if (!isset($calleridnum)) {
                                $calleridnum = "";
                            }
                            if (!isset($channel_state)) {
                                $channel_state = "";
                            }
                            if (!isset($channel_state_desc)) {
                                $channel_state_desc = "";
                            }
                            if (!isset($seconds)) {
                                $seconds = "";
                            }
                            //echo "STATUS\n";
                            /*
                             Event: Status
                             Privilege: Call
                             Channel: SIP/matt-000000ae
                             CallerIDNum: matt
                             CallerIDName: Matt
                             ConnectedLineNum: <unknown>
                             ConnectedLineName: <unknown>
                             Accountcode: 
                             ChannelState: 6
                             ChannelStateDesc: Up
                             Context: internal
                             Extension: 600
                             Priority: 2
                             Seconds: 3
                             Uniqueid: 1346190746.205
                             */
                            /*echo "=================\n";
                            echo "Channel: $channel\n";
                            echo "CallerIDNum: $calleridnum\n";
                            echo "CallerIDName: $calleridname\n";
                            echo "ChannelState: $channel_state\n";
                            echo "ChannelStateDesc: $channel_state_desc\n";
                            echo "Context: $context\n";
                            echo "Extension: $extension\n";
                            echo "Priority: $priority\n";
                            echo "Seconds: $seconds\n";
                            echo "UniqueID: $uniqueid\n";
                            echo "=================\n";*/
                            status($channel, $calleridnum, $calleridname, $channel_state, $channel_state_desc, $uniqueid, $seconds);
                        } else if ($eventname == "Leave") {
                        } else if ($eventname == "Hangup") {
                            /*
                             Event: Hangup
                             Privilege: call,all
                             Channel: SIP/phono4-ext.voxeolabs.net-00000090
                             Uniqueid: 1346187286.175
                             CallerIDNum: 79786527-9c2c-4384-a1a4-f409cfe39a08
                             CallerIDName: <unknown>
                             ConnectedLineNum: <unknown>
                             ConnectedLineName: <unknown>
                             Cause: 0
                             Cause-txt: Unknown
                             */
                            hangup($channel, $uniqueid);
                        } else if ($eventname == "CoreShowChannelsComplete") {
                        } else if ($eventname == "CoreShowChannel") {
                            /*echo "====================\n";
                            echo "Unique ID: ".$uniqueid."\n";
                            echo "App Data: ".$application_data."\n";
                            echo "CID Num: ".$calleridnum."\n";
                            echo "CID Name: ".$calleridname."\n";
                            echo "Duration: ".$duration."\n";
                            echo "AcctCode: ".$accountcode."\n";
                            echo "Bridged Channel: ".$bridgedchannel."\n";
                            echo "Bridged Unique ID: ".$bridgeduniqueid."\n";
                            echo "Event List: ".$eventlist."\n";
                            echo "List Items: ".$listitems."\n";
                            echo "======================================\n";*/
                            core_show_channels($uniqueid,$application_data,$calleridnum,$calleridname,$duration,$accountcode,$bridgedchannel,$bridgeduniqueid);                            
                        } else if ($eventname == "Shutdown") {
                            // Server is shutting down - force a reconnect
                            fclose($socket);
                        } else if ($eventname == "QueueMemberPaused") {
                            queue_member_paused($member_name, $paused, $queue);
                        } else if ($eventname == "QueueMemberAdded") {
                            queue_member_status($member_name, $queue, $location, $membership, $calls_taken, $last_call, $status, $paused, $penalty);                        
                        } else if ($eventname == "QueueMemberRemoved") {
                            queue_member_status($member_name, $queue, $location, $membership, $calls_taken, $last_call, $status, $paused, $penalty);                        
                        } else if ($eventname == "Dial") {
                        } else if ($eventname == "Newexten") {
                        } else if ($eventname == "Registry") {
                        } else if ($eventname == "QueueCallerAbandon") {                            
                        } else if ($eventname == "FullyBooted") {
                        } else if ($eventname == "MusicOnHold") {
                        } else if ($eventname == "Agentlogin") {
                        } else if ($eventname == "Agentlogoff") {
                        } else if ($eventname == "NewAccountCode") {
                        } else if ($eventname == "NewCallerid") {
                        } else {
                            if ($DEBUG_UNKNOWN) {
                                echo "Unknown event: $eventname\n";
                            }
                        }
                        
                        /* we need to clear out all the variables for the next event */
                        $event = false;
                        $eventname = "";
                        $peer_name = "";
                        $peer_status = "";
                        $cause = "";
                        $time = "";
                        $chan_1 = "";
                        $chan_2 = "";
                        $uniq_1 = "";
                        $uniq_2 = "";
                        $clid_1 = "";
                        $clid_2 = "";
                        unset ($queue);
                        unset ($location);
                        unset ($member_name);
                        unset ($membership);
                        unset ($calls_taken);
                        unset ($last_call);
                        unset ($status);
                        unset ($paused);
                        unset ($channel);
                        unset ($state);
                        unset ($clid);
                        unset ($channel);
                        unset ($state);
                        unset ($clid);
                        unset ($uniqueid);
                        unset ($clid_name);
                        unset ($remote_agent);
                        unset ($remote_agent_disconnect);
                        unset ($clid_num);
                        unset ($context);
                        unset ($extension);
                        unset ($priority);
                        unset ($application);
                        unset ($app_data);
                        unset ($cause_text);
                        unset ($user_event);
                        unset ($channel_driver);
                        unset ($domain);
                        unset ($penalty);
                        unset ($privilege);
                        unset ($count);
                        unset ($position);
                        unset ($cid_calling_pres);
                        unset ($uniqueid);
                        unset ($source);
                        unset ($destination);
                        unset ($src_unique_id);
                        unset ($dest_unique_id);
                        unset ($hold_time);
                        unset ($original_position);
                        unset ($channel_type);
                        unset ($channel_state);
                        unset ($channel_state_desc);
                        unset ($connected_line_num);
                        unset ($connected_line_name);
                        unset ($bridgetype);
                        unset ($uniqueid);
                        unset ($application_data);
                        unset ($calleridnum);
                        unset ($calleridname);
                        unset ($duration);
                        unset ($accountcode);
                        unset ($bridgedchannel);
                        unset ($bridgeduniqueid);
                        unset ($eventlist);
                        unset ($listitems);
                        unset ($items);
                        unset ($accountcode);
                        unset ($seconds);
                        unset ($address);
                        unset ($exten);
                        unset ($bridged_uniqueid);
                        unset ($account);
                        unset ($class);
                        unset ($agent);
                        unset ($old_account_code);
                        unset ($bridge_state);
                        unset ($login_time);
                    } else { /* This is not a blank line but we are currently in an event */
                        if (substr($line, 0, 9) == "Privilege") {
                            $privilege = substr($line,0,10);
                        } else if (substr($line, 0, 10) == "Logintime:") {
                            $login_time = substr($line, 11);
                        } else if (substr($line, 0, 12) == "Bridgestate:") {
                            $bridge_state = substr($line, 13);
                        } else if (substr($line, 0, 16) == "BridgedUniqueid:") {
                            $bridged_uniqueid = substr($line, 17);
                        } else if (substr($line, 0, 15) == "OldAccountCode:") {
                            $old_account_code = substr($line, 16);
                        } else if (substr($line, 0, 6) == "Class:") {
                            $class = substr($line, 7);
                        } else if (substr($line, 0, 6) == "Agent:") {
                            $agent = substr($line, 7);
                        } else if (substr($line, 0, 8) == "Account:") {
                            $account = substr($line, 9);
                        } else if (substr($line, 0, 6) == "Exten:") {
                            $exten = substr($line, 7);
                        } else if (substr($line, 0, 11) == "Bridgetype:") {
                            $bridgetype = substr($line, 12);
                        } else if (substr($line, 0, 8) == "Address:") {
                            $address = substr($line, 9);
                        } else if (substr($line, 0, 9) == "UniqueID:") {
                            $uniqueid = substr($line, 10);
                        } else if (substr($line, 0, 6) == "Items:") {
                            $items = substr($line, 7);
                        } else if (substr($line, 0, 12) == "Accountcode:") {
                            $accountcode = substr($line, 13);
                        } else if (substr($line, 0, 8) == "Seconds:") {
                            $seconds = substr($line, 9);
                        } else if (substr($line, 0, 16) == "ApplicationData:") {
                            $application_data = substr($line, 17);
                        } else if (substr($line, 0, 12) == "CallerIDnum:") {
                            $calleridnum = substr($line, 13);
                        } else if (substr($line, 0, 10) == "EventList:") {
                            $eventlist = substr($line, 11);
                        } else if (substr($line, 0, 10) == "ListItems:") {
                            $listitems = substr($line, 11);
                        } else if (substr($line, 0, 13) == "CallerIDname:") {
                            $calleridname = substr($line, 14);
                        } else if (substr($line, 0, 9) == "Duration:") {
                            $duration = substr($line, 10);
                        } else if (substr($line, 0, 12) == "AccountCode:") {
                            $accountcode = substr($line, 13);
                        } else if (substr($line, 0, 15) == "BridgedChannel:") {
                            $bridgedchannel = substr($line, 16);
                        } else if (substr($line, 0, 16) == "BridgedUniqueID:") {
                            $bridgeduniqueid = substr($line, 17);
                        } else if (substr($line, 0, 12) == "ChannelType:") {
                            $channel_type = substr($line, 13);
                        } else if (substr($line, 0, 13) == "ChannelState:") {
                            $channel_state = substr($line, 14);
                        } else if (substr($line, 0, 17) == "ChannelStateDesc:") {
                            $channel_state_desc = substr($line, 18);
                        } else if (substr($line, 0, 17) == "ConnectedLineNum:") {
                            $connected_line_num = substr($line, 17);
                        } else if (substr($line, 0, 18) == "ConnectedLineName:") {
                            $connected_line_name = substr($line, 18);
                        } else if (substr($line, 0, 17) == "OriginalPosition:") {
                            $original_position = substr($line, 18);
                        } else if (substr($line, 0, 9) == "HoldTime:") {
                            $hold_time = substr($line, 10);
                        } else if (substr($line, 0, 9) == "Uniqueid:") {
                            $uniqueid = substr($line, 10);
                        } else if (substr($line, 0, 7) == "Source:") {
                            $source = substr($line, 8);
                        } else if (substr($line, 0, 12) == "Destination:") {
                            $destination = substr($line, 12);
                        } else if (substr($line, 0, 12) == "SrcUniqueID:") {
                            $src_unique_id = substr($line, 13);
                        } else if (substr($line, 0, 13) == "DestUniqueID:") {
                            $dest_unique_id = substr($line, 14);
                        } else if (substr($line, 0, 9) == "Position:") {
                            $position = substr($line, 10);
                        } else if (substr($line, 0, 6) == "Count:") {
                            $count = substr($line, 7);
                        } else if (substr($line, 0, 16) == "CID-CallingPres:") {
                            $cid_calling_pres = substr($line, 17);
                        } else if (substr($line, 0, 5) == "Peer:") {
                            $peer_name = substr($line, 6);
                        } else if (substr($line, 0, 11) == "PeerStatus:") {
                            $peer_status = substr($line, 12);
                        } else if (substr($line, 0, 6) == "Cause:") {
                            $cause = substr($line, 7);
                        } else if (substr($line, 0, 5) == "Time:") {
                            $time = substr($line, 6);
                        } else if (substr($line, 0, 9) == "Channel1:") {
                            $chan_1 = substr($line, 10);
                        } else if (substr($line, 0, 9) == "Channel2:") {
                            $chan_2 = substr($line, 10);
                        } else if (substr($line, 0, 10) == "Uniqueid1:") {
                            $uniq_1 = substr($line, 11);
                        } else if (substr($line, 0, 10) == "Uniqueid2:") {
                            $uniq_2 = substr($line, 11);
                        } else if (substr($line, 0, 10) == "CallerID1:") {
                            $clid_1 = substr($line, 11);
                        } else if (substr($line, 0, 10) == "CallerID2:") {
                            $clid_2 = substr($line, 11);
                        } else if (substr($line, 0, 6) == "Queue:") {
                            $queue = substr($line, 7);
                        } else if (substr($line, 0, 9) == "Location:") {
                            $location = substr($line, 10);
                        } else if (substr($line, 0, 11) == "MemberName:") {
                            $member_name = substr($line, 12);
                        } else if (substr($line, 0, 11) == "Membership:") {
                            $membership = substr($line, 12);
                        } else if (substr($line, 0, 11) == "CallsTaken:") {
                            $calls_taken = substr($line, 12);
                        } else if (substr($line, 0, 9) == "LastCall:") {
                            $last_call = substr($line, 10);
                        } else if (substr($line, 0, 7) == "Status:") {
                            $status = substr($line, 8);
                        } else if (substr($line, 0, 7) == "Paused:") {
                            $paused = substr($line, 8);
                        } else if (substr($line, 0, 10) == "UserEvent:") {
                            $user_event = substr($line, 11);
                        } else if (substr($line, 0, 8) == "Channel:") {
                            $channel = substr($line, 9);
                        } else if (substr($line, 0, 6) == "State:") {
                            $state = substr($line, 7);
                        } else if (substr($line, 0, 9) == "CallerID:") {
                            $clid = substr($line, 10);
                        } else if (substr($line, 0, 13) == "CallerIDName:") {
                            $clid_name = substr($line, 14);
                        } else if (substr($line, 0, 12) == "RemoteAgent:") {
                            $remote_agent = substr($line, 13);
                        } else if (substr($line, 0, 22) == "RemoteAgentDisconnect:") {
                            $remote_agent_disconnect = substr($line, 23);
                        } else if (substr($line, 0, 9) == "Uniqueid:") {
                            $unique_id = substr($line, 10);
                        } else if (substr($line, 0, 12) == "CallerIDNum:") {
                            $clid_num = substr($line, 13);
                        } else if (substr($line, 0, 8) == "Context:") {
                            $context = substr($line, 9);
                        } else if (substr($line, 0, 10) == "Extension:") {
                            $extension = substr($line, 11);
                        } else if (substr($line, 0, 9) == "Priority:") {
                            $priority = substr($line, 10);
                        } else if (substr($line, 0, 12) == "Application:") {
                            $application = substr($line, 13);
                        } else if (substr($line, 0, 8) == "AppData:") {
                            $app_data = substr($line, 9);
                        } else if (substr($line, 0, 10) == "Cause-txt:") {
                            $cause_txt = substr($line, 11);
                        } else if (substr($line, 0, 14) == "ChannelDriver:") {
                            $channel_driver = substr($line, 15);
                        } else if (substr($line, 0, 7) == "Domain:") {
                            $domain = substr($line, 8);
                        } else if (substr($line, 0, 8) == "Penalty:") {
                            $penalty = substr($line, 9);
                        } else {
                            if ($DEBUG_UNKNOWN)
                            {
                                echo "==] Unknown variable: $line [==\n";
                            }
                        }
                        
                        /*
                         Event: Newstate
                         Privilege: call,all
                         Channel: IAX2/6848602-16384
                         State: Ringing
                         CallerID: 0212742112
                         CallerIDName: <unknown>
                         Uniqueid: dndn.venturevoip.co-1236245517.1537
                         */
                        
                    }
                }
                
                
            }
            
        }
    }
    /* We have fallen through from the connection - this means the connection has dropped */
    /* Close the socket, wait a few seconds and then start again                                                  */
    fclose($socket);
    echo "+===================================+\n";
    echo "| Disconnected by remote host...    |\n";
    echo "+===================================+\n";
    sleep(3);
    echo "| Reconnecting to manager...        |\n";
}
?>
