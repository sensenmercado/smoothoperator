<?
session_start();
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
<script src="http://s.phono.com/releases/0.4/jquery.phono.js"></script>
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
function login(call) {
    <?
    $agent_num = $_SESSION['agent_num'];
    $agent_pass = $_SESSION['agent_pass'];
    
    for ($i = 0;$i<strlen($agent_num);$i++) {
        echo 'call.digit("'.substr($agent_num,$i,1).'");';
    }
    echo 'call.digit("#");';
    for ($i = 0;$i<strlen($agent_pass);$i++) {
        echo 'call.digit("'.substr($agent_pass,$i,1).'");';
    }
    echo 'call.digit("#");';
    ?>
    $("#status").html("Logged in.");
    var objx = call;
    $("#hangup").click(function() {
                       call.hangup();
                       });
    $("#disconnect").click(function() {
                           call.digit("*");
                           });
}
$(document).ready(function(){
                  var audioType = 'auto';
                  if (navigator.javaEnabled()) {
                  audioType = 'java';
                  }
                  
                  var phono = $.phono({
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
                                      var call = event.call;
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
                  
                  
                  
                  
                  
                  
                  
                  /* $("#hangup").click(function() {
                   
                   
                   var out = '';
                   for (var i in phono.phone) {
                   out += i + ": " + phono.phone[i] + "\n";
                   }
                   var pre = document.createElement('pre');
                   pre.innerHTML = out;
                   document.body.appendChild(pre);
                   
                   
                   
                   //phono.hangup();
                   });*/
                  $("#call").click(function() {
                                   $("#call").attr("disabled", true);
                                   $("#disconnect").attr("disabled", false);
                                   $("#hangup").attr("disabled", false);
                                   $("#testing").text("Receiving Calls");
                                   $("#pause_buttons").show();
                                   phono.phone.dial("sip:500@<?=$_SESSION['config_values']['manager_host']?>", {
                                                    onRing: function() {
                                                    $("#status").html("Ringing");
                                                    },
                                                    onAnswer: function() {
                                                    setTimeout(login(this),1000);
                                                    },
                                                    onHangup: function() {
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
</script>
<input id="call" type="button" disabled="true" value="Loading..." /><br />
<div id="pause_buttons" style="display: none;font-family: arial">
<button type="button" id="pause" value="Pause" onclick="jQuery('#testing').load('phono.php?pause=true');jQuery('#pause').hide();jQuery('#unpause').show();" style="text-decoration: none;height: 32px;vertical-align: middle; padding: 8px"><img src="images/control_pause_blue.png" align="bottom" width="16" height="16"/>&nbsp;Pause</button>
<button type="button" value="Resume" id="unpause" onclick="jQuery('#testing').load('phono.php?pause=false');jQuery('#pause').show();jQuery('#unpause').hide();" style="display: none;text-decoration: none;height: 32px;vertical-align: middle; padding: 8px"><img src="images/control_play_blue.png" width="16" height="16" align="bottom" />&nbsp;Unpause</button>
</div>
<input id="disconnect" type="button" disabled = "true" value="Disconnect caller" /><br />
<input id="hangup" type="button" disabled="true" value="Logout" /><br />
<script type='text/javascript' src='js/jquery-1.3.2.min.js'></script>
<script>
jQuery.noConflict();
</script><p style="font-family: arial">
Status:</p>

<div id="testing" style="text-align: center;font-family: arial;color: #f00">
Waiting for login<br />
<br /><a href="#" onclick="location.reload();">Reload Softphone</a>
</div>
<br />
</div>
</body>
</html>
