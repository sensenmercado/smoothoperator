<?
require "header.php";
if (isset($_GET['save'])) {
    $sql = "INSERT INTO lists (name, description) VALUES (".sanitize($_POST['name']).",".sanitize($_POST['description']).")";
    $result = mysqli_query($connection, $sql);
    redirect("manage_lists.php", 0);    
}
if (isset($_GET['add'])) {
    ?>
    <div class="thin_700px_box">
    <h2>Add a list</h2>
    <form action="manage_lists.php?save=1" method="post">
    <table class="sample">
    <tr><td>List Name: </td><td><input type="text" name="name" style="width: 400px" ></td></tr>
    <tr><td colspan="2">List Description:</td></tr>
    <tr><td colspan="2"><textarea cols="60" rows="10" name="description"></textarea></td></tr>
    <tr><td colspan="2"><input type="submit" value="Create Record"></td></tr>
    </table>
    </form>
    </div>
    <?
    require "footer.php";
    exit(0);
}
if (!isset($_GET['list'])) {
    ?>
    <div class="thin_700px_box" >
    <?
    $result  = mysqli_query($connection, "SELECT * FROM lists");
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            
            
            
            
            
            
            $printed_header = false;
            echo "<table border=\"1\" class=\"sample2\" width=\"100%\" style=\"background: #eee\">";
            $printable[] = "name";
            while ($row = mysqli_fetch_assoc($result)) {
                /* If we haven't printed the table header, do that first */
                if (!$printed_header) {
                    $printed_header = true;
                    echo "<tr>";
                    foreach ($row as $field=>$value) {
                        if (in_array($field, $printable)) {
                            echo "<th>".ucwords(strtolower(str_replace("_", " ",$field)))."</th>";
                        }
                    }
                    echo "</tr>";
                }
                $result2 = mysqli_query($connection, "SELECT Count(*) as total FROM customers WHERE list_id = ".$row['id']);
                if (mysqli_num_rows($result2) > 0) {
                    $row2 = mysqli_fetch_assoc($result2);
                    $count = $row2['total'];
                }

                echo "<tr onmouseover=\"this.style.background='#bcf ';this.style.cursor='pointer'\" onmouseout=\"this.style.background='#eee';\" onclick=\"window.location.href='manage_lists.php?edit=".$row['id']."';\">";
                foreach ($row as $field=>$value) {
                    if (in_array($field, $printable)) {
                        echo "<td>".$value." ($count numbers)</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo 'You have no lists of numbers. <a href="manage_lists.php?add=1">Click here</a> to create one';            
        }
    } else {
        echo 'The lists table is missing - you shouldn\'t be here';        
    }
    ?>
    </div>
    <?
    require "footer.php";
    exit(0);
}

?>
<?
require "footer.php";
?>
