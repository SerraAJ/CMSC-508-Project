
<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}
require_once "config.php";
$lookup_con_name =  $_SESSION["lookup_con_name"];
$lookup_con_number = $_SESSION["lookup_con_number"];
$sql = "SELECT * FROM conventions WHERE convention_name = '". $lookup_con_name."' AND convention_number = ".$lookup_con_number;

$result = mysqli_query($conn, $sql);
echo "<table>";
echo "<tr> <th>Convention Name</th> <th>Number</th><th>Start Date</th><th>End Date</th><th>Theme</th>
    <th>Venue Name</th> <th>Venue ID</th></tr>";
while($row = mysqli_fetch_array($result))
{
    $name = $row['convention_name'];
    $number = $row['convention_number'];
    $start = $row['start_date'];
    $end = $row['end_date'];
    $theme = $row['convention_theme'];
    $type = $row['convention_type'];
    $venue_id = $row['venue_id'];
    $sql2 = "SELECT name FROM convention_centers WHERE venue_id =" . $venue_id;
    $result2 = mysqli_query($conn, $sql2);
    $venue_name = mysqli_fetch_object($result2);
  
    
    echo"<tr><td style='width: 300px;'>".$name.
    "</td><td style='width: 40px;'>".$number.
    "</td><td style='width: 100px;'>".$start.
    "</td><td style='width: 100px;'>".$end.
    "</td><td style='width: 200px;'>".$type.
    "</td><td style='width: 200px;'>".$theme.
    "</td><td style='width: 400px;'>".$venue_name.
    "</td><td style='width: 50px;'>".$venue_id."</td></tr>";
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
<a href = "viewConventions.php">Click here to return to the list of Conventions.</a>
</body>
</html>