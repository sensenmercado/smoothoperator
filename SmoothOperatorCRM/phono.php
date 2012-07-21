<?
session_start();
echo "<pre>";
print_r($_SESSION);
?>
<html>
<head>
<script src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
<script src="http://s.phono.com/releases/0.3/jquery.phono.js"></script>
</head>
<body>

<input id="call" type="button" disabled="true" value="Loading..." />
<span id="status"></span>

<script>
$(document).ready(function(){
                  var audioType = 'auto';
                  if (navigator.javaEnabled()) {
                  audioType = 'java';
                  }
                  
                  var phono = $.phono({
                                      apiKey: "<?=$_SESSION['config_values']['phono_key']?>",
                                      audio: {type:audioType,
                                      jar:"http://s.phono.com/releases/0.3/plugins/audio/phono.audio.jar"},
                                      onReady: function() {
                                      //alert("My SIP address is sip:" + this.sessionId);
                                      $("#xxx").val(this.sessionId);
                                      $("#call").attr("disabled", false).val("Call");
                                      },
                                      phone: {
                                      
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
                  
                  $("#call").click(function() {
                                   $("#call").attr("disabled", true).val("Busy");
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
                                                    //alert(event.call.id);
                                                    //$("#status").html(event.call.id);
                                                    var out = '';
                                                    for (var i in this) {
                                                    out += i + ": " + this[i] + "\n";
                                                    }
                                                    var pre = document.createElement('pre');
                                                    pre.innerHTML = out;
                                                    document.body.appendChild(pre);
                                                    
                                                    },
                                                    onHangup: function() {
                                                    $("#call").attr("disabled", false).val("Call");
                                                    $("#status").html("Hangup");
                                                    }
                                                    });
                                   });
                  })
</script>
<input id="xxx" type="text">
</body>
</html>
