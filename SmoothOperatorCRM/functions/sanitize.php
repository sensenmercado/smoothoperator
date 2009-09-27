<?
        if (!function_exists('sanitize') ) {
        function sanitize($var, $quotes = true) {
        if (is_array($var)) {   //run each array item through this function (by reference)
            foreach ($var as &$val) {
                $val = $this->sanitize($val);
            }
        }
        else if (is_string($var)) { //clean strings
            $var = mysql_real_escape_string($var);
            if ($quotes) {
                $var = "'". $var ."'";
            }
        }
        else if (is_null($var)) {   //convert null variables to SQL NULL
            $var = "NULL";
        }
        else if (is_bool($var)) {   //convert boolean variables to binary boolean
            $var = ($var) ? 1 : 0;
        }
        return $var;
    }
}
?>
