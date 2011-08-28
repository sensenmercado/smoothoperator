<?
/* 
 * The idea here is to load and process customer records using scripts. This
 * differs from normal displaying of customer records in much the same way as a
 * mail merge works.
 *
 * A script is a series of statements or questions that the call centre staff
 * member will read as they respond to a customer.  This way you can have staff
 * that are working simultaneously on multiple projects.  It can be useful for
 * outbound marketing as well as inbound calls as it allows for an easy way to
 * store information.
 *
 * A script is based on the following MySQL structure:
 *
 * id - autoincrement index (automatically created)
 * name - name of the script
 * description - description of the script
 * owner - the id of the user who created this script
 * lastupdated - automatically updated timestamp for last update
 * groupid - the id of a group of people allowed access to this script
 *
 * You then have script entries which are stored in the script_entries table:
 *
 * id - autoincrement index (automatically created)
 * script_id - id of the script that this refers to
 * type - the type of script entry i.e.:
 *       -1 - end of section/page
 *        0 - statement followed by text field
 *        1 - statement followed by a yes/no field
 *        2 - statement followed by a combo box field
 *        3 - statement followed by nothing
 * statement - the text to display
 * order - the position of the entry in the script as a whole
 *
 * If you are using a combo box field, the choices are supplied from the
 * script_choices table.  This table has the following structure:
 *
 * id - autoincrement index (automatically created)
 * script_id - id of the script that this refers to
 * text - the text of the choice (i.e. 'apple' or 'pear')
 * value - the value to be used when saving to database - i.e. without space
 *
 */

require "header.php";



require "footer.php";

?>
