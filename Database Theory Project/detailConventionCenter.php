<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}
require_once "config.php";
$lookup_venue_id =  $_SESSION["lookup_venue_id"];
$sql = "SELECT * FROM convention_centers WHERE venue_id = " . $lookup_venue_id;

$result = mysqli_query($conn, $sql);
echo "<table>";
echo "<tr> <th>Name</th> <th>ID</th><th>Address</th><th>Phone Number</th><th>Number of Floors</th></tr>";
while($row = mysqli_fetch_array($result))
{
    $name = $row['name'];
    $address = $row['address'];
    $phone_number = $row['phone_number'];
    $floors = $row['floors'];
    if(is_null($floors) || empty($floors))
    {
        $floors = "Not Listed";
    }
    
    echo"<tr><td style='width: 300px;'>".$name.
        "</td><td style='width: 200px;'>".$lookup_venue_id.
        "</td><td style='width: 400px;'>".$address.
        "</td><td style='width: 200px;'>".$phone_number.
        "</td><td style='width: 200px;'>".$floors."</td></tr>";
}
echo "</table>";
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<style>
th {text-align: left;}
th, td {padding: 15px;}
table, th, td {border: 1px solid black;}
</style>
<body>
<br><br>
<a href = "viewConventionCenter.php">Click here to return to the list of Convention Centers.</a>
</body>
</html>