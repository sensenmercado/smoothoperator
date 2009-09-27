<?
if (!function_exists('open_page') ) {
     function open_page($url,$f=1,$c=2,$r=0,$a=0,$cf=0,$pd=""){
     global $oldheader;
     $url = str_replace("http://","",$url);
     if (preg_match("#/#","$url")){
      $page = $url;
      $url = @explode("/",$url);
      $url = $url[0];
      $page = str_replace($url,"",$page);
      if (!$page || $page == ""){
       $page = "/";
      }
      $ip = gethostbyname($url);
     }else{
      $ip = gethostbyname($url);
      $page = "/";
     }
     $open = fsockopen($ip, 80, $errno, $errstr, 60);
     if ($pd){
      $send = "POST $page HTTP/1.0\r\n";
     }else{
      $send = "GET $page HTTP/1.0\r\n";
     }
     $send .= "Host: $url\r\n";
     if ($r){
      $send .= "Referer: $r\r\n";
     }else{
      if ($_SERVER['HTTP_REFERER']){
       $send .= "Referer: {$_SERVER['HTTP_REFERER']}\r\n";
      }
     }
     if ($cf){
      if (@file_exists($cf)){
       $cookie = urldecode(@file_get_contents($cf));
       if ($cookie){
        $send .= "Cookie: $cookie\r\n";
        $add = @fopen($cf,'w');
        fwrite($add,"");
        fclose($add);
       }
      }
     }
     $send .= "Accept-Language: en-us, en;q=0.50\r\n";
     if ($a){
      $send .= "User-Agent: $a\r\n";
     }else{
      $send .= "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n";
     }
     if ($pd){
      $send .= "Content-Type: application/x-www-form-urlencoded\r\n";
      $send .= "Content-Length: " .strlen($pd) ."\r\n\r\n";
      $send .= $pd;
     }else{
      $send .= "Connection: Close\r\n\r\n";
     }
     fputs($open, $send);
     while (!feof($open)) {
      $return .= fgets($open, 4096);
     }
     fclose($open);
     $return = @explode("\r\n\r\n",$return,2);
     $header = $return[0];
     if ($cf){
      if (preg_match("/Set\-Cookie\: /i","$header")){
       $cookie = @explode("Set-Cookie: ",$header,2);
       $cookie = $cookie[1];
       $cookie = explode("\r",$cookie);
       $cookie = $cookie[0];
       $cookie = str_replace("path=/","",$cookie[0]);
       $add = @fopen($cf,'a');
       fwrite($add,$cookie,strlen($read));
       fclose($add);
      }
     }
     if ($oldheader){
      $header = "$oldheader<br /><br />\n$header";
     }
     $header = str_replace("\n","<br />",$header);
     if ($return[1]){
      $body = $return[1];
     }else{
      $body = "";
     }
     if ($c === 2){
      if ($body){
       $return = $body;
      }else{
       $return = $header;
      }
     }
     if ($c === 1){
      $return = $header;
     }
     if ($c === 3){
      $return = "$header$body";
     }
     if ($f){
      if (preg_match("/Location\:/","$header")){
       $url = @explode("Location: ",$header);
       $url = $url[1];
       $url = @explode("\r",$url);
       $url = $url[0];
       $oldheader = str_replace("\r\n\r\n","",$header);
       $l = "&#76&#111&#99&#97&#116&#105&#111&#110&#58";
       $oldheader = str_replace("Location:",$l,$oldheader);
       return open_page($url,$f,$c,$r,$a,$cf,$pd);
      }else{
       return $return;
      }
     }else{
      return $return;
     }
    }
}
?>
