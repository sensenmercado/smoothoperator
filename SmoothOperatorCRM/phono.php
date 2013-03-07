<?
session_start();
if (isset($_GET['pickup'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    require "functions/asterisk_manager.php";
    $config_values = $_SESSION['config_values'];
    $sql = "SELECT channel FROM parked_calls WHERE room = ".sanitize($_GET['pickup']);
    $result = mysqli_query($connection, $sql) or die(json_encode(mysqli_error($connection)));
    $row = mysqli_fetch_assoc($result);
    bridge($row['channel'],$_GET['me']);
    exit(0);
}

if (isset($_GET['at_xfer'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    require "functions/asterisk_manager.php";
    $sql = "SELECT bridged_channel FROM channels WHERE channel = ".sanitize($_GET['at_xfer'])." limit 1";
    //echo json_encode($sql);
    //exit(0);
    $result = mysqli_query($connection, $sql) or die(json_encode(mysqli_error($connection)));
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $channel = $row['bridged_channel'];
    }
    $config_values = $_SESSION['config_values'];
    $context = "outbound_crm";
    $exten = '4072674434';
    //$context = "conference";
    //$exten = "777";
    $priority = "1";
    //$channel = $_GET['at_xfer'];
    $result = at_xfer($channel, $context, $exten, $priority);
    echo json_encode($result);
    exit(0);
}


if (isset($_GET['park_call'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    require "functions/asterisk_manager.php";
    $config_values = $_SESSION['config_values'];
    $channel1 = $_GET['channel1'];
    $channel2 = $_GET['channel2'];
    sleep(10);
    //    $sql = "SELECT bridged_channel FROM channels WHERE channel = ".sanitize($_GET['channel1'])." limit 1";
    //    $result = mysqli_query($connection, $sql) or die(json_encode(mysqli_error($connection)));
    /*    if (mysqli_num_rows($result) == 1) {
     $row = mysqli_fetch_assoc($result);
     $channel1 = $row['bridged_channel'];
     }*/
    $result = mysqli_query($connection, "SELECT * FROM parked_calls order by room desc limit 1");
    if (mysqli_num_rows($result) == 0) {
        $slot = 7000;
    } else {
        $row = mysqli_fetch_assoc($result);
        $slot = $row['room']+1;
    }
    $result = mysqli_query($connection, "INSERT INTO parked_calls (room, channel, agent, parked_at) VALUES (".$slot.",".sanitize($channel1).",".sanitize($_SESSION['agent_num']).",NOW())");
    // 4 hours
    $timeout = 1000 * 60 * 60 * 4;
    park_call($channel1, $channel2, $slot, $timeout);
    echo json_encode($slot);
    exit(0);
}
if (isset($_GET['check_parked'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    $result = mysqli_query($connection, "SELECT * FROM parked_calls WHERE agent = ".sanitize($_SESSION['agent_num']));
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    }
    exit(0);
}
if (isset($_GET['transfer_to_conf_call'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    require "functions/asterisk_manager.php";
    $config_values = $_SESSION['config_values'];
    $sql = "SELECT bridged_channel FROM channels WHERE channel = ".sanitize($_GET['transfer_to_conf_call'])." limit 1";
    $result = mysqli_query($connection, $sql) or die(json_encode(mysqli_error($connection)));
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $channel2 = $row['bridged_channel'];
        $channel1 = $_GET['transfer_to_conf_call'];
        $context = "conference";
        $extension = $_SESSION['agent_num'];
        $priority = "1";
        transfer_to_extension($channel1, $channel2, $context, $extension, $priority);
        //        transfer_single_to_extension($channel1, $context, $extension, $priority);
        //        transfer_single_to_extension($channel2, $context, $extension, $priority);
        echo json_encode($row['bridged_channel']);
        //echo json_encode("Done");
    } else {
        echo json_encode("Can't find second channel");
    }
    exit(0);
}
if (isset($_GET['get_channel'])) {
    require "config/db_config.php";
    require "functions/sanitize.php";
    sleep(3);
    $result = mysqli_query($connection, "SELECT data1 FROM queue_log WHERE event = 'AGENTLOGIN' AND agent = 'Agent/".$_SESSION['agent_num']."' order by id desc limit 1") or die(json_encode(mysqli_error($connection)));
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row['data1']);
    } else {
        echo json_encode("0");
    }
    exit(0);
}
if (strlen($_SESSION['config_values']['phono_key']) <1) {
    // TODO: This won't work on a new install
    ?>
    <script>
    top.location.href = "login.php" ;
    </script>
    <?
    require "footer.php";
    exit(0);
}
$config_values = $_SESSION['config_values'];
require "functions/asterisk_manager.php";
if (isset($_GET['pause'])) {
    //echo "<pre>";
    if ($_GET['pause'] != "true") {
        $_GET['pause'] = "false";
    }
    $result = asterisk_agent_change_status($_GET['pause']);
    //    echo "done";
    if ($_GET['pause'] == "true") {
        echo "Agent Paused";
    } else {
        echo "Receiving Calls";
    }
    exit(0);
}
?>
<html>
<head>
<script src="js/jquery-1.4.2.min.js"></script>
<?/*<script src="js/jquery.phono.0.3.js"></script>*/?>
<script src="http://s.phono.com/releases/0.6/jquery.phono.js"></script>
<style>
body {
padding: 0px;
margin:0px;
}
#content {
margin: 0px;
padding: 10px;
background-image: linear-gradient(bottom, rgb(255,255,255) 0%, rgb(116,157,222) 100%);
background-image: -o-linear-gradient(bottom, rgb(255,255,255) 0%, rgb(116,157,222) 100%);
background-image: -moz-linear-gradient(bottom, rgb(255,255,255) 0%, rgb(116,157,222) 100%);
background-image: -webkit-linear-gradient(bottom, rgb(255,255,255) 0%, rgb(116,157,222) 100%);
background-image: -ms-linear-gradient(bottom, rgb(255,255,255) 0%, rgb(116,157,222) 100%);

background-image: -webkit-gradient(
                                   linear,
                                   left bottom,
                                   left top,
                                   color-stop(0, rgb(255,255,255)),
                                   color-stop(1, rgb(116,157,222))
                                   );
}
</style>
</head>
<body bgcolor="#fff"><div id="content" style="100%">
<center>
<span id="heading" style="font-family: arial">
Soft Phone
</span><br /><br >
<span id="status" style="font-family: arial"><img src="images/small_progress.gif"></span>
<br /><br />
<script>
var my_uniqueid;
var phono;
var bridged_channel;
var call;
//get the file
$("#hangup").click(function() {
                   alert("bla");
                   });
function get_parked_calls() {
    $.ajax({
           type: "GET",
           context: document.body,
           url: "phono.php?check_parked=1",
           dataType: "json",
           error : function(data) {
           alert("Unable to get channel: "+data);
           },
           success : function(data) {
           //my_uniqueid = data;
           //$("#status_light").css("background-color","#0f0");
           //$("#testing").append(data);
           //alert(data);
           }
           });
    
}
function callParked(parked_id) {
    // Make link to retrieve/transfer call
    $("#status_light").css("background-color","#f00");
    // Make a new call to the transfer number
    //phono.phone.dial("sip:600@<?=$_SESSION['config_values']['manager_host']?>");
    $("#parked_calls").html('<a href="#" onclick="$(\'#testing\').load(\'phono.php?pickup='+parked_id+'&me='+my_uniqueid+'\');">'+parked_id+'</a>');
    /*
     // Get our new channel id
     $.ajax({
     type: "GET",
     context: document.body,
     url: "phono.php?get_channel=1",
     dataType: "json",
     error : function(data) {
     alert("Unable to get channel: "+data);
     },
     success : function(data) {
     my_uniqueid = data;
     $("#status_light").css("background-color","#0f0");
     $("#testing").append(data);
     $("#parked_calls").html('<a href="#" onclick="$(\'#testing\').load(\'phono.php?pickup='+parked_id+'&me='+my_uniqueid+'\');">'+parked_id+'</a>');
     }
     });
     */
    
}
function park() {
    
    
    alert("Parking...");
    transfer();
    
    //$("#testing").load(");
    //    alert("parked");
    
}

function login(callx) {
    //call = callx;
    <?
    $agent_num = $_SESSION['agent_num'];
    $agent_pass = $_SESSION['agent_pass'];
    
    for ($i = 0;$i<strlen($agent_num);$i++) {
        echo 'call.digit("'.substr($agent_num,$i,1).'");'."\n";
    }
    echo 'call.digit("#");';
    for ($i = 0;$i<strlen($agent_pass);$i++) {
        echo 'call.digit("'.substr($agent_pass,$i,1).'");'."\n";
    }
    echo 'call.digit("#");';
    ?>
    $("#status_light").css("background-color","#ffa500");
    
    //alert(1);
    $.ajax({
           type: "GET",
           context: document.body,
           url: "phono.php?get_channel=1",
           dataType: "json",
           error : function(data) {
           alert("Unable to get channel: "+data);
           },
           success : function(data) {
           my_uniqueid = data;
           $("#status_light").css("background-color","#0f0");
           //$("#testing").append(data);
           }
           });
    
    
    $("#status").html("Logged in.");
    var objx = call;
    $("#hangup").click(function() {
                       //alert("hangup");
                       call.hangup();
                       });
    /*
     $("#disconnect").click(function() {
     call.digit("*");
     });
     */
    
    
}

$(document).ready(function(){
                  var audioType = 'java';
                  /*if (navigator.javaEnabled()) {
                  audioType = 'java';
                  }*/
                  
                  phono = $.phono({
                                  apiKey: "<?=$_SESSION['config_values']['phono_key']?>",
                                  audio: {
                                  type:audioType,
                                  jar:"http://s.phono.com/releases/0.4/plugins/audio/phono.audio.jar"},
                                  onReady: function() {
                                  //alert("My SIP address is sip:" + this.sessionId);
                                  //$("#status").text(this.sessionId);
                                  $("#status").html("Click the button below to log in");
                                  $("#testing").text("Waiting for login");
                                  $("#call").attr("disabled", false).val("Login");
                                  },
                                  phone: {
                                  wideband: false,
                                  headset: true,
                                  
                                  onIncomingCall: function(event) {
                                  call = event.call;
                                  console.log("Auto-answering call with ID " + call.id);
                                  // Answer the call
                                  call.answer();
                                  var out = '';
                                  for (var i in call) {
                                  out += i + ": " + call[i] + "\n";
                                  }
                                  var pre = document.createElement('pre');
                                  pre.innerHTML = out;
                                  document.body.appendChild(pre);
                                  
                                  }
                                  }
                                  });
                  
                  $("#call").click(function() {
                                   $("#call").attr("disabled", true);
                                   $("#disconnect").attr("disabled", false);
                                   $("#hangup").attr("disabled", false);
                                   $("#testing").text("Receiving Calls");
                                   $("#pause_buttons").show();
                                   call = phono.phone.dial("sip:500@<?=$_SESSION['config_values']['manager_host']?>", {
                                                    onRing: function() {
                                                    $("#status").html("Ringing");
                                                    },
                                                    onAnswer: function() {
                                                           //alert("hello");
                                                    setTimeout(login(this),5000);
                                                    },
                                                    onHangup: function() {
                                                    $("#status_light").css("background-color","#f00");
                                                    $("#call").attr("disabled", false).val("Login");
                                                    $("#hangup").attr("disabled", true);
                                                    $("#disconnect").attr("disabled", true);
                                                    $("#testing").text("Waiting for Login");
                                                    $("#pause_buttons").hide();
                                                    $("#status").html("Logged Out.  Please click the button below to log back in");
                                                    }
                                                    });
                                   });
                  })
function transfer() {
    alert("About to transfer");
    $.ajax({
           type: "GET",
           context: document.body,
           url: "phono.php?transfer_to_conf_call="+my_uniqueid,
           dataType: "json",
           error : function(jqXHR, textStatus, errorThrown) {
           alert("Unable to get transfer: "+textStatus);
           },
           success : function(data) {
           bridged_channel = data;
           alert("My Chan: "+my_uniqueid+" Bridged Chan: "+bridged_channel);
           $.get("phono.php?park_call=1&channel2="+my_uniqueid+"&channel1="+bridged_channel, callParked);
           
           }
           });
}
function completeTransfer() {
    
    var str;
    str = $("#transfer_number").val();
    for (var i = 0, len = str.length; i < len; i++) {
        call.digit(str[i]);
    }
    return;
    
    /*    call.digit("4");
     call.digit("0");
     call.digit("7");
     call.digit("2");
     call.digit("6");
     call.digit("7");
     call.digit("4");
     call.digit("4");
     call.digit("3");
     call.digit("4");*/
}
function at_xfer() {
    call.digit("#");
    call.digit("#");
    setTimeout(completeTransfer, 1000);
    //alert("About to transfer");
    /*    $.ajax({
     type: "GET",
     context: document.body,
     url: "phono.php?at_xfer="+my_uniqueid,
     dataType: "json",
     error : function(jqXHR, textStatus, errorThrown) {
     alert("Unable to get transfer: "+textStatus);
     },
     success : function(data) {
     //alert(data);
     }
     });*/
}
</script>
<input id="call" type="button" disabled="true" value="Loading..." /><br />
<div id="pause_buttons" style="display: none;font-family: arial">
<button type="button" id="pause" value="Pause" onclick="jQuery('#testing').load('phono.php?pause=true');jQuery('#pause').hide();jQuery('#unpause').show();" style="text-decoration: none;height: 32px;vertical-align: middle; padding: 8px"><img src="images/control_pause_blue.png" align="bottom" width="16" height="16"/>&nbsp;Pause</button>
<button type="button" value="Resume" id="unpause" onclick="jQuery('#testing').load('phono.php?pause=false');jQuery('#pause').show();jQuery('#unpause').hide();" style="display: none;text-decoration: none;height: 32px;vertical-align: middle; padding: 8px"><img src="images/control_play_blue.png" width="16" height="16" align="bottom" />&nbsp;Resume</button>
<?/*<button type="button" onclick="alert(my_uniqueid);">What is my channel</button>
   
   <button type="button" onclick="transfer();$('#disconnect_conf').show()">Transfer to Conf</button>
   <button id="disconnect_conf">Disconnect Conference Call</button>
   <button id="park" onclick="park();">Park Call</button>
   */?>
Transfer Number: <input type="text" id="transfer_number">
<button id="transfer" onclick="at_xfer();">Transfer Call</button>
</div>
<input id="disconnect" type="button" disabled = "true" value="Disconnect caller" onclick = "call.digit('*');" /><br />
<input id="hangup" type="button" disabled="true" value="Logout" /><br />
<script type='text/javascript' src='js/jquery-1.3.2.min.js'></script>
<script>
jQuery.noConflict();
</script><p style="font-family: arial">
Status:</p>
<span id="blabla" onclick="call.digit('3');">Bla</span>
<div id="testing" style="text-align: center;font-family: arial;color: #f00">
Waiting for login<br />
<br /><a href="#" onclick="location.reload();">Reload Softphone</a>
</div>
<br />
<?/*<div id="parked_calls_header" style="font-family: arial">Parked Calls:</div>
   <div id="parked_calls"></div>*/?>
<br />
</div>
<span id="status_light" style="display: block;width:10px;height:10px;background-color:#f00; position: absolute;bottom: 0px;right: 0px"></span>
</body>
</html>
