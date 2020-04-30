<?php
require "config.php";
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
    $inspect_venue_id = test_input($_POST["select_id"]);
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
?>

<!DOCTYPE html>
<html>
<body>

<h1>View Existing Venues</h1>
<br>
<h2>Select a venue to view more details.</h2>
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

<select id = "venues" name="venues">
<option value = "0">Test?</option>
<?php 
require "config.php";
$sql = "SELECT  venue_id, name FROM convention_centers";
$results = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($results))
{
    echo "<option> $row[name]  $row[venue_id]</option>";
}
mysqli_close($conn);
?>
</select>
<span class = "error"> <?php echo $inspect_venue_id_error; ?></span>
<br><br>



<input type = "submit" name = "submit" value = "Submit">

</form>

<br><br>
 <a href = "welcome.php">Go back to the welcome page by clicking here.</a>

</body>
</html>