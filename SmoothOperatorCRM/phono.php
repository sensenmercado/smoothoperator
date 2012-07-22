<?
session_start();
?>
<html>
<head>
<script src="js/jquery-1.4.2.min.js"></script>
<?/*<script src="js/jquery.phono.0.3.js"></script>*/?>
<script src="http://s.phono.com/releases/0.4/jquery.phono.js"></script>
</head>
<body bgcolor="#ccc">
<center>
<span id="heading" style="font-family: arial">
Soft Phone
</span><br />
<br />
<input id="call" type="button" disabled="true" value="Loading..." /><br />
<input id="hangup" type="button" disabled="true" value="Logout" /><br />
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
                                      audio: {type:audioType,
                                      jar:"http://s.phono.com/releases/0.3/plugins/audio/phono.audio.jar"},
                                      onReady: function() {
                                      //alert("My SIP address is sip:" + this.sessionId);
                                      //$("#status").text(this.sessionId);
                                      $("#status").text("Click the button above to log in");
                                      $("#call").attr("disabled", false).val("Login");
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
                                                    },
                                                    onHangup: function() {
                                                    $("#call").attr("disabled", false).val("Login");
                                                    $("#hangup").attr("disabled", true);
                                                    $("#status").html("Logged Out.  Please click the button above to log back in");
                                                    }
                                                    });
                                   });
                  })
</script>
</body>
</html>
