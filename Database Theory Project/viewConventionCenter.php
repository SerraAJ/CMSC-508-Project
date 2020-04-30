<?php
require_once "config.php";
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}

$inspect_venue_id = "";
$inspect_venue_id_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if($_POST["venues"] == "-")
    {
        $inspect_venue_id_error = "Please choose a database from the dropdown list.";
    }
    else
    {
    $inspect_venue_id = test_input($_POST["venues"]);
    if(strrpos($inspect_venue_id, " ", 0) == false)
    {
        $inspect_venue_id_error = "You must select a database from the list to see its details.";
    }
    else 
    {
        $inspect_venue_id = substr( $inspect_venue_id, (strrpos($inspect_venue_id, " "))); 
        
        $sql = "SELECT venue_id FROM convention_centers WHERE venue_id = ?";
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "i", $inspect_venue_id);
            if(mysqli_stmt_execute($stmt) == 1)
            {
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1)
                {
                    $_SESSION["lookup_venue_id"] = $inspect_venue_id;
                    header("location: detailConventionCenter.php");
                    
                }
                else
                {
                    echo "Something's gone wrong; no venue with that ID found.";
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

<h1>View Existing Venues</h1>
<br>
<h2>Select a venue to view more details.</h2>
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

<select id = "venues" name="venues">
<option>-</option>
<?php 
$sql = "SELECT  venue_id, name FROM convention_centers";
$results = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($results))
{
    echo "<option> $row[name]  $row[venue_id]</option>";
}

?>
</select>
<span class = "error"> <?php echo $inspect_venue_id_error; ?></span>
<br><br>



<input type = "submit" name = "submit" value = "View Details">

</form>

<br><br>
 <a href = "welcome.php">Go back to the welcome page by clicking here.</a>

<br>
<table>
<tr> <th>Venue Name</th><th>Venue ID</th><th>Address</th></tr>
<?php 
$sql = "SELECT name, venue_id, address FROM convention_centers";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($result))
{
    $name = $row['name'];
    $venue_id = $row['venue_id'];
    $address = $row['address'];
    echo"<tr><td style='width: 300px;'>".$name.
    "</td><td style='width: 200px;'>".$venue_id.
    "</td><td style='width: 400px;'>".$address."</td></tr>";
    
}
?>
</table>
</body>
</html>