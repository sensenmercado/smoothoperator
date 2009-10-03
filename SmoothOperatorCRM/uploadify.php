<?
/* This is the PHP file that receives a file that you upload */

/* Used to get a connection to the database */
require "config/db_config.php";

/* Used to provide the sanitize() function for cleaning input */
require "functions/sanitize.php";

if (!empty($_FILES)) {
	$temp_file = $_FILES['Filedata']['tmp_name'];
        $file_name = $_FILES['Filedata']['name'];
        $file_size = $_FILES['Filedata']['size'];
        $file_type = $_FILES['Filedata']['type'];

        /* Create a filename based on the md5 of the file and the current     */
        /* time.  This filename means that even if the same file is uploaded  */
        /* it will exist as a separate entity for the person who uploaded it  */
        /* Otherwise, if one person uploaded a file, and another uploaded the */
        /* same file, and subsequently deleted it, the original uploader      */
        /* would be unable to use their file.                                 */
        $new_filename = md5_file($temp_file);
        $new_filename = "uploads/upload-".$new_filename.".".time();;

        /* Save the information about this file to the database */
        $result = mysqli_query($connection, "INSERT INTO files (filename, location, size, type, date_imported) VALUES (".sanitize($file_name).", ".sanitize($new_filename).", ".sanitize($file_size).", ".sanitize($file_type).", NOW())");

        /* Copy the temp file to the new location */
        copy($temp_file, $new_filename);

        /* Report that the upload succeeded */
        echo "1";
}
?>