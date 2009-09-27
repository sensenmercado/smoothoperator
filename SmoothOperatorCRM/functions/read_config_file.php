<?


if (file_exists($config_file)) {
    $fp = fopen($config_file, "r");
    while (!feof($fp)) {
      $line = trim(fgets($fp));
      if ($line && substr($line,0,1)!=$comment) {
        $pieces = explode("=", $line);
        $option = trim($pieces[0]);
        $value = trim($pieces[1]);
        $config_values[$option] = $value;
      }
    }
    fclose($fp);
}

if ($config_values['FILL_STYLE'] == "") {
    $config_values['FILL_STYLE'] = "gradient";
}

?>
