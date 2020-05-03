<?php

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}
require "config.php";
$sql = "CREATE TABLE IF NOT EXISTS rooms(
    venue_id        INT NOT NULL,
    room_number     CHAR(5) NOT NULL,
    floor           CHAR(3) NOT NULL,
    name            VARCHAR(100),
    maximum_occupancy   INT(4) NOT NULL,
    PRIMARY KEY (venue_id, room_number),
    FOREIGN KEY (venue_id) REFERENCES convention_centers(venue_id)
    
)";

if(mysqli_query($conn, $sql)){
    
} else{
    echo "ERROR: Could not  execute $sql. " . mysqli_error($conn);
}

$room_number = "";
$floor = "";
$room_name = "";
$maximum_occupancy = "";
$room_number_error = "";
$floor_error = "";
$room_name_error = "";
$maximum_occupancy_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $room_number = test_input($_POST["room_number"]);
    if(empty($room_number))
    {
        $room_number_error = "You must enter the room number.";
    }
    elseif(strlen($room_number) > 5)
    {
        $room_number_error = "Room number must be 5 or fewer characters.";
    }
    else
    {
        $sql = "SELECT room_number FROM rooms WHERE room_number = ? AND venue_id = ?";
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "si", $room_number, $_SESSION["lookup_venue_id"]);
            if(mysqli_stmt_execute($stmt) == 1)
            {
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1)
                {
                    $room_number_error = "There is already a room with this number in this venue.";
                }
            }
            mysqli_stmt_close($stmt);
        }
        else
        {
            echo "Something went wrong checking the database. Please try again.";
        }
        
    }
    
    $floor = test_input($_POST["floor"]);
    if(empty($room_number))
    {
        $floor_error = "You must enter the floor, or 0 if there are no floors in the venue.";
    }
    elseif(strlen($floor) > 3)
    {
        $address_error = "Floor must be 3 or fewer characters.";
    }
    
    $room_name = test_input($_POST["room_name"]);
    if(strlen($room_number) > 100)
    {
        $room_name_error = "Room name must be 100 or fewer characters.";
    }
    
    $maximum_occupancy = test_input($_POST["maximum_occupancy"]);
    if(empty($maximum_occupancy))
    {
        $maximum_occupancy_error = "You must enter the max occupancy of the room.";
    }
    else if(!ctype_digit($maximum_occupancy))
    {
        $maximum_occupancy_error = "Please enter the max occupancy as a number with no other characters.";
    }
    elseif($maximum_occupancy < 1)
    {
        $maximum_occupancy_error = "Max occupancy must be at least 1.";
    }
    elseif($maximum_occupancy > 9999)
    {
        $maximum_occupancy_error = "Max occupancy must be at most 9999.";
    }
    
    if(empty($room_number_error) && empty($floor_error) && empty($room_name_error) && empty($maximum_occupancy_error))
    {
        echo "Preparing SQL..."
        $sql = "INSERT INTO rooms (venue_id, room_number, floor, room_name, maximum_occupancy) VALUES (?, ?, ?, ?, ?,)";
        if($stmt = mysqli_prepare($conn, $sql))
        {
            echo "Binding sql...";
           
            mysqli_stmt_bind_param($stmt, "isssi", $_SESSION["lookup_venue_id"], $room_number, $floor, $room_name, $maximum_occupancy);
            echo "executing...";
            if(mysqli_stmt_execute($stmt))
            {
                echo "sql executed.";
                if(empty($room_name))
                {
                    $room_name = $room_number;
                }
                $_SESSION["bounce_message"] = ("Successfully added room " . $room_name . " on floor ". $floor . " to venue " . $_SESSION["lookup_venue_id"] . ".");
                header("location: detailConventionCenter.php");
            }
            else
            {
                echo "Something went wrong executing the statement to add this to the database. Please try again.";
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
<h1>Register a new room for venue <?php echo $_SESSION["lookup_venue_id"]?></h1>
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

Room Number: <input type = "text" name = "room_number">
<span class = "error">* <?php echo $room_number_error; ?></span>
<br><br>

Room Name (if different): <input type = "text" name = "room_name">
<span class = "error"> <?php echo $room_name_error; ?></span>
<br><br>

Floor: <input type = "text" name = "floor">
<span class = "error">* <?php  echo $floor_error; ?></span>
<br><br>

Maximum Occupancy: <input type = "number" min =  "0" max = "9999" name = "maximum_occupancy">
<span class = "error">* <?php  echo $maximum_occupancy_error; ?></span>
<br><br>


<input type = "submit" name = "submit" value = "Submit">

<br><br>
</form>
<a href = "detailConventionCenter.php">Go back to the details for <?php echo $_SESSION["lookup_venue_id"]?>.</a>

<br>
<a href = "welcome.php">Go back to the welcome page by clicking here.</a>

</body>
</html>