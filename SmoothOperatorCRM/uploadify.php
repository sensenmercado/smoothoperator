<?
require "config/db_config.php";
require "functions/sanitize.php";
if (!empty($_FILES)) {
	$temp_file = $_FILES['Filedata']['tmp_name'];
        $file_name = $_FILES['Filedata']['name'];
        $file_size = $_FILES['Filedata']['size'];
        $file_type = $_FILES['Filedata']['type'];
        /*$fp      = fopen($temp_file, 'r');
        $content = fread($fp, filesize($temp_file));
        $content = addslashes($content);
        fclose($fp);*/
        $new_filename = md5_file($temp_file);
        $new_filename = "uploads/upload-".$new_filename.".".time();;
        //$result = mysqli_query($connection, "INSERT INTO lists (filename, file, size, type, date_imported) VALUES (".sanitize($file_name).", '".$content."', ".sanitize($file_size).", ".sanitize($file_type).", NOW())");
        $result = mysqli_query($connection, "INSERT INTO files (filename, location, size, type, date_imported) VALUES (".sanitize($file_name).", ".sanitize($new_filename).", ".sanitize($file_size).", ".sanitize($file_type).", NOW())");
        //echo "1";
        copy($temp_file, $new_filename);
        echo "1";
}
?>