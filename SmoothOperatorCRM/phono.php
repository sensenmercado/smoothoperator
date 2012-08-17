<?
session_start();
if (strlen($_SESSION['config_values']['phono_key']) <1) {
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
    echo "<pre>";
    if ($_GET['pause'] != "true") {
        $_GET['pause'] = "false";
    }
    $result = asterisk_agent_change_status($_GET['pause']);
//    echo "done";
    if ($_GET['pause'] == "true") {
        echo "Status: Agent Paused";
    } else {
        echo "Status: Receiving Calls";
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
</span><br />
<br />
<input id="call" type="button" disabled="true" value="Loading..." /><br />
<input id="disconnect" type="button" disabled = "true" value="Disconnect caller" /><br />
<input id="hangup" type="button" disabled="true" value="Pause" /><br />
<br />
<span id="status" style="font-family: arial"><img src="images/small_progress.gif"></span>

<script>
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
                                      $("#status").text("Click the button above to log in");
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
                                   phono.phone.dial("sip:500@<?=$_SESSION['config_values']['manager_host']?>", {
                                                    onRing: function() {
                                                    $("#status").html("Ringing");
                                                    },
                                                    onAnswer: function() {
                                                    <?
                                                    $agent_num = $_SESSION['agent_num'];
                                                    $agent_pass = $_SESSION['agent_pass'];
                                                    
                                                    for ($i = 0;$i<strlen($agent_num);$i++) {
                                                    echo 'this.digit("'.substr($agent_num,$i,1).'");';
                                                    }
                                                    echo 'this.digit("#");';
                                                    for ($i = 0;$i<strlen($agent_pass);$i++) {
                                                    echo 'this.digit("'.substr($agent_pass,$i,1).'");';
                                                    }
                                                    echo 'this.digit("#");';
                                                    ?>
                                                    $("#status").html("Logged in.");

                                                    //alert(event.call.id);
                                                    //$("#status").html(event.call.id);
                                                    /*
                                                    var out = '';
                                                    for (var i in this) {
                                                    out += i + ": " + this[i] + "\n";
                                                    }
                                                    var pre = document.createElement('pre');
                                                    pre.innerHTML = out;
                                                    document.body.appendChild(pre);
                                                    */
                                                    var objx = this;
                                                    $("#hangup").click(function() {
                                                                       objx.hangup();
                                                                       });
                                                    $("#disconnect").click(function() {
                                                                       objx.digit("*");
                                                                       });
                                                    },
                                                    onHangup: function() {
                                                    $("#call").attr("disabled", false).val("Unpause");
                                                    $("#hangup").attr("disabled", true);
                                                    $("#disconnect").attr("disabled", true);
                                                    $("#status").html("Logged Out.  Please click the button above to log back in");
                                                    }
                                                    });
                                   });
                  })
</script>
<?
if (isset($_GET['debug'])) {
    ?>
    <script type='text/javascript' src='js/jquery-1.3.2.min.js'></script>
    <script>
    jQuery.noConflict();
    </script>
    <br />
    <br />
Status:
    <br />    <br />

    <div id="testing" style="text-align: center">
Receiving Calls
    </div>
    <br />
    <a href="#" id="pause" onclick="jQuery('#testing').load('phono.php?pause=true');jQuery('#pause').hide();jQuery('#unpause').show();">&nbsp;<img src="images/control_pause_blue.png">Pause</a><br />
    <a href="#" id="unpause" onclick="jQuery('#testing').load('phono.php?pause=false');jQuery('#pause').hide();jQuery('#unpause').show();" style="display: none">Unpause</a>
    <?
}
?>
</div>
</body>
</html>
