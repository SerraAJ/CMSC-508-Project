<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}
if(isset($_SESSION ["bounce_message"]))
{
    $message = $_SESSION["bounce_message"];
    echo $message;
}

echo "\nWhat would you like to do?\n \n"




?>

<!DOCTYPE html>
<html>
<body>


<br>
<a href = "createConvention.php">Create a new Convention or Event!</a>
<br>
<a href = "viewConventions.php">View or manage existing Conventions</a>
<br>
<a href = "createConventionCenter.php">Register a Convention Center or Venue</a>
<br>
<a href = "viewConventionCenter.php">View Information for Venues</a>
<br>
<a href = "logout.php">Log out of this account.</a>



</body>
</html>