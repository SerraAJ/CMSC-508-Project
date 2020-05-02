<?php
require_once "config.php";
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}
$inspect_con = "";
$inspect_con_name = "";
$inspect_con_number = "";
$inspect_con_error = "";


if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if($_POST["conventions"] == "-")
    {
        $inspect_con_error = "Please choose a convention from the dropdown list.";
    }
    else
    {
        $inspect_con = test_input($_POST["conventions"]);
        if(strrpos($inspect_con, " ", 0) == false)
        {
            $inspect_conerror = "You must select a database from the list to see its details.";
        }
        else
        {
            $inspect_con_number = substr( $inspect_con, (strrpos($inspect_con, " ")));
            $inspect_con_name = substr($inspect_con, 0, strlen($inspect_con) - 2 - strrpos($inspect_con, ""));
            
            $sql = "SELECT * FROM convention_centers WHERE convention_name = ? AND convention_number = ?";
            if($stmt = mysqli_prepare($conn, $sql))
            {
                mysqli_stmt_bind_param($stmt, "ss", $inspect_con_name, $inspect_con_number);
                if(mysqli_stmt_execute($stmt) == 1)
                {
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 1)
                    {
                        $_SESSION["lookup_con_name"] = $inspect_con_name;
                        $_SESSION["lookup_con_number"] = $inspect_con_number;
                        header("location: detailConvention.php");
                        
                    }
                    else
                    {
                        echo "Something's gone wrong; no convention with those details found.";
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<style>
th {text-align: left;}
th, td {padding: 15px;}
table, th, td {border: 1px solid black;}
</style>
<body>

<h1>View Existing Conventions</h1>
<br>
<h2>Select a convention to view more details.</h2>
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

<select id = "conventions" name="conventions">
<option>-</option>
<?php 
$sql = "SELECT  convention_name, convention_number 
    FROM conventions GROUP BY convention_name ORDER BY convention_number";
$results = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($results))
{
    echo "<option> $row[convention_name]  $row[convention_number]</option>";
}

?>
</select>
<span class = "error"> <?php echo $inspect_con_error; ?></span>
<br><br>



<input type = "submit" name = "submit" value = "View Details">

</form>

<br><br>
 <a href = "welcome.php">Go back to the welcome page by clicking here.</a>

<br>
<table>
<tr> <th>Convention Name</th><th>Number</th><th>Convention Type</th><th>Start Date</th></tr>
<?php 
$sql = "SELECT  convention_name, convention_number, convention_type, start_date
    FROM conventions GROUP BY convention_name ORDER BY convention_number";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($result))
{
    $name = $row['convention_name'];
    $number = $row['convention_number'];
    $type = $row['convention_type'];
    $start = $row['start_date'];
    echo"<tr><td style='width: 300px;'>".$name .
    "</td><td style='width: 50px;'>".$number .
    "</td><td style='width: 100px;'>".$type .
    "</td><td style='width:100px;'>".$start."</td></tr>";
    
}
?>
</table>
</body>
</html>