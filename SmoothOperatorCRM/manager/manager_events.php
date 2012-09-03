<?
function agent_disconnected ($agent_number) {
    require "../functions/sanitize.php";
	/* An agent has been disconnected */
	global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
	echo "Agent Disconnected: $agent_number\n";
	if ($FILE_BACKEND) {
        $fh = fopen($remote_agent_file.$remote_agent_disconnect.".txt", 'w') or die("We were unable to write to the ConduIT");
        fwrite($fh, "Disconnect");
        fclose($fh);
	}
	if ($MYSQL_BACKEND) {
		$sql = "DELETE FROM agent_call_status WHERE agent = ".sanitize($agent_number);
        mysqli_query($connection, $sql);
        if (mysqli_error($connection)) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
	}
}
function agent_connected ($agent_number, $queue_name, $callerid) {
	/* An agent has been connected */
    require "../functions/sanitize.php";
    
	global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
	echo "== Agent Connected: $agent_number ==\n";
	if ($FILE_BACKEND) {
	    $fh = fopen($remote_agent_file.$agent_num.".txt", 'w') or die("We were unable to write to the ConduIT");
	    fwrite($fh, $cidname."!".$cidnum."\n");
	    fclose($fh);
	}
	if ($MYSQL_BACKEND) {
		$sql = "REPLACE INTO agent_call_status (agent, queue, callerid) VALUES (";
		$sql.= sanitize($agent_number).", ".sanitize($queue_name).", ".sanitize($callerid).")";
        mysqli_query($connection, $sql);
        if (mysqli_error($connection)) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
	}
    
}
function queue_member_status($member_name, $queue, $location, $membership, $calls_taken, $last_call, $status, $paused, $penalty) {
    require "../functions/sanitize.php";
    
	global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
    /* DEVICE STATES */
    $device_states[1] = "Unknown";
    $device_states[3] = "Not In Use";
    $device_states[6] = "In Use";
    $device_states[7] = "Busy";
    $device_states[0] = "Invalid";
    $device_states[2] = "Unavailable";
    $device_states[5] = "Ringing";
    $device_states[8] = "Ring In Use";
    $device_states[4] = "On Hold";
	/*echo "=====================================\n";
	echo "Queue Member Status\n";
	echo "=====================================\n";
	echo "Member: $member_name\n";
	echo "Queue: $queue\n";
	echo "Location: $location\n";
	echo "Membership: $membership\n";
	echo "Calls Taken: $calls_taken\n";
	echo "Last Call: $last_call\n";
	echo "Status: $status\n";
	echo "Text Status: ".$device_states[$status]."\n";
	echo "Paused: $paused\n";
	echo "Penalty: $penalty\n";
	echo "=====================================\n";*/
	if ($MYSQL_BACKEND) {
		if ($status == 0) {
			$sql = "DELETE FROM queue_member_status WHERE member = ".sanitize($member_name);
            //			$sql.= sanitize($member_name).", ".sanitize($queue).", ".sanitize($location).", ".sanitize($membership).", ".sanitize($calls_taken).", ".sanitize($status).", ".sanitize($paused).", ".sanitize($penalty).")";
		} else {
			$sql = "REPLACE INTO queue_member_status (member,queue,location,membership,calls_taken,status,paused,penalty) VALUES (";
			$sql.= sanitize($member_name).", ".sanitize($queue).", ".sanitize($location).", ".sanitize($membership).", ".sanitize($calls_taken).", ".sanitize($status).", ".sanitize($paused).", ".sanitize($penalty).")";
		}
        mysqli_query($connection, $sql);
        if (mysqli_error($connection)) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
	}
}
function queue_member_paused($member_name, $paused, $queue) {
    require "../functions/sanitize.php";
	global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
	echo "QUEUE MEMBER PAUSED: $member_name, $paused, $queue\n";
	if ($MYSQL_BACKEND) {
		$sql = "UPDATE queue_member_status set paused = ".sanitize($paused)." WHERE queue = ".sanitize($queue)." AND member = ".sanitize($member_name);
		echo "SQL: ".$sql."\n";
        mysqli_query($connection, $sql);
        if (mysqli_error($connection)) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
	}
}
function peer_status ($peer_name, $peer_status, $cause, $time, $cause_txt) {
	global $FILE_BACKEND, $MYSQL_BACKEND, $DEBUG_PEER_STATUS, $connection;
	if ($DEBUG_PEER_STATUS) {
		echo "=====================================\n";
		echo "Peer Status\n";
		echo "=====================================\n";
		echo "Peer: $peer_name\n";
		echo "Status: $peer_status\n";
		echo "Cause: $cause\n";
		echo "Cause Text: $cause_txt\n";
		echo "Time: $time\n";
		echo "=====================================\n";
	}
}

function status($channel, $calleridnum, $calleridname, $channelstate, $channelstatedesc, $uniqueid, $seconds) {
    if (strlen($uniqueid) > 0) {
        global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
        require "../functions/sanitize.php";
        $sql = "REPLACE INTO SmoothOperator.channels (uniqueid,cid_name,cid_num,duration,channel_state, channel_state_desc, channel) VALUES (".sanitize($uniqueid).",".sanitize($calleridnum).",".sanitize($calleridname).",".sanitize($seconds).",".sanitize($channelstate).",".sanitize($channelstatedesc).",".sanitize($channel).")";
        //echo "Running $sql";
        mysqli_query($connection, $sql);
        if (mysqli_error($connection)) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
        
    }
}
function core_show_channels($uniqueid,$application_data,$calleridnum,$calleridname,$duration,$accountcode,$bridgedchannel,$bridgeduniqueid) {
    if (strlen($uniqueid) > 0) {
        global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
        require "../functions/sanitize.php";
        $sql = "UPDATE SmoothOperator.channels set accountcode=".sanitize($accountcode).",bridged_channel=".sanitize($bridgedchannel).",bridged_uniqueid=".sanitize($bridgeduniqueid)." where uniqueid = ".sanitize($uniqueid);
        //echo "Running $sql";
        mysqli_query($connection, $sql);
        if (@mysqli_error($connection)||!$connection) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
        
    }
}

function asterisk_link($chan_1, $chan_2, $clid_1, $clid_2, $type = "Unknown", $bridgetype = "") {
	global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
	echo "=====================================\n";
	echo "Channel Link from $type via $bridgetype\n";
	echo "=====================================\n";
	echo "Chan 1: $chan_1\n";
	echo "Chan 2: $chan_2\n";
	echo "CallerID 1: $clid_1\n";
	echo "CallerID 2: $clid_2\n";
	echo "=====================================\n";
    if (substr($chan_1,0,5) == "Local") {
        echo "Leg 1 was local, returning\n";
        return;
    }
    if (substr($chan_2,0,5) == "Local") {
        echo "Leg 2 was local, returning\n";
        return;
    }
    /*if (trim($bridgetype) == "core") {
        echo "Bridge Type was core, returning\n";
        return;
    }*/
    echo "Local not detected - continuing\n";
    if (substr($chan_1,0,5) == "Agent") {
        $sql = "INSERT INTO SmoothOperator.phone_calls (callerid, extension) VALUES ('".$clid_2."','".substr($chan_1,6)."')";
        //echo "Running $sql";
        mysqli_query($connection, $sql);
        if (mysqli_error($connection)) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
    } else if (substr($chan_2,0,5) == "Agent") {
        $sql = "INSERT INTO SmoothOperator.phone_calls (callerid, extension) VALUES ('".$clid_1."','".substr($chan_2,6)."')";
        //echo "Running $sql";
        mysqli_query($connection, $sql);
        if (mysqli_error($connection)) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
    }
}

function hangup($channel, $uniqueid) {
    global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
    require "../functions/sanitize.php";
	$sql = "DELETE FROM SmoothOperator.channels WHERE uniqueid = ".sanitize($uniqueid);
    mysqli_query($connection, $sql);
    if (mysqli_error($connection)) {
        echo "Error running, running again...";
        require "../config/db_config.php";
        mysqli_query($connection, $sql);
    }
}

function asterisk_unlink($chan_1, $chan_2, $clid_1, $clid_2) {
	global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
	echo "=====================================\n";
	echo "Channel Unlink\n";
	echo "=====================================\n";
	echo "Chan 1: $chan_1\n";
	echo "Chan 2: $chan_2\n";
	echo "CallerID 1: $clid_1\n";
	echo "CallerID 2: $clid_2\n";
	echo "=====================================\n";
    if (substr($chan_1,0,5) == "Agent") {
        $sql = "INSERT INTO SmoothOperator.hangups (extension) VALUES ('".substr($chan_1,6)."')";
        //echo "Running $sql";
        mysqli_query($connection, $sql);
        if (mysqli_error($connection)) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
    } else if (substr($chan_2,0,5) == "Agent") {
        $sql = "INSERT INTO SmoothOperator.hangups (extension) VALUES ('".substr($chan_2,6)."')";
        echo "Running $sql";
        mysqli_query($connection, $sql);
        if (mysqli_error($connection)) {
            require "../config/db_config.php";
            mysqli_query($connection, $sql);
        }
    }
}
function new_state($peer_name, $state, $callerid, $callerid_name, $unique_id) {
	global $FILE_BACKEND, $MYSQL_BACKEND, $connection;
	/*echo "=====================================\n";
	echo "New Call State\n";
	echo "=====================================\n";
	echo "Peer: $peer_name\n";
	echo "State: $state\n";
	echo "CallerID Number: $callerid\n";
	echo "CallerID Name: $callerid_name\n";
	echo "UniqueID: $unique_id\n";
	echo "=====================================\n";*/
}
?>