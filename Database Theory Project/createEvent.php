<?php

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}
require "config.php";
$sql = "CREATE TABLE IF NOT EXISTS events(
    event_name      VARCHAR(100) NOT NULL,
    convention_name VARCHAR(100) NOT NULL,
    convention_number   VARCHAR(4) NOT NULL,
    start_time      DATETIME NOT NULL,
    end_time        DATETIME NOT NULL,
    description     VARCHAR(400),
    room_number     CHAR(5) NOT NULL,
    venue_id        INT NOT NULL,
    event_type      VARCHAR(30) NOT NULL,
    INDEX(venue_id, room_number),
    INDEX(convention_name, convention_number),
    PRIMARY KEY (event_name, convention_name, convention_number),
    FOREIGN KEY (venue_id, room_number) REFERENCES rooms(venue_id, room_number),
    FOREIGN KEY (convention_name, convention_number) REFERENCES conventions(convention_name, convention_number)
    
)";

if(mysqli_query($conn, $sql)){
    
} else{
    echo "ERROR: Could not  execute $sql. " . mysqli_error($conn);
}
/*$sql = "
        
        CREATE PROCEDURE createConventionServices (
            IN i_event_name VARCHAR(100),
            IN i_convention_name VARCHAR(100),
            IN i_convention_number VARCHAR(4),
            IN i_start_time DATETIME,
            IN i_end_time DATETIME,
            IN i_description VARCHAR(400),
            IN i_room_number CHAR(5) )
        BEGIN
        CREATE TABLE IF NOT EXISTS convention_services (
            event_name      VARCHAR(100) NOT NULL,
            convention_name VARCHAR(100) NOT NULL,
            convention_number   VARCHAR(4) NOT NULL,
            INDEX(event_name, convention_name, convention_number),
            PRIMARY KEY(event_name, convention_name, convention_number),
            FOREIGN KEY(event_name, convention_name, convention_number) REFERENCES events(event_name, convention_name, convention_number) );
        INSERT INTO events 
            VALUES(i_event_name, i_convention_name, i_convention_number, i_start_time, i_end_time, i_description, 
                i_room_number,'Convention Services');
        INSERT INTO convention_services 
            VALUES (i_event_name, i_convention_name, i_convention_number);
        END;
        
";
if(mysqli_query($conn, $sql)){
    
} else{
    echo "ERROR: Could not  execute Convention Services procedure. " . mysqli_error($conn);
}*/

$event_name="";
$convention_name = $_SESSION["lookup_con_name"];
$convention_number = $_SESSION["lookup_con_number"];
$start_time="";
$end_time ="";
$description="";
$room_number ="";
$is_type_convention_services = 0;
$is_type_child_event= 0;
$is_type_main_event= 0;
$is_type_adult_only = 0;
$is_type_shopping_area = 0;
$is_type_paid_entry = 0;
$is_type_video_viewing = 0;
$is_type_crafting = 0;
$is_type_panel = 0;
$is_type_cosplay_show = 0;

$event_name_error = "";
$start_time_error = "";
$end_time_error = "";
$description_error = "";
$room_number_error = "";
$type_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $event_name = test_input($_POST["event_name"]);
    if(empty($event_name))
    {
        $event_name_error = "You must enter a name for this event.";
    }
    elseif( strlen($event_name) > 100)
    {
        $event_name_error = "This name is too long. Must be 100 or fewer characters.";
    }
    
    
    $start_time = new DateTime($_POST["start_time"]);
    $end_time = new DateTime($_POST["end_time"]);
    $current_time = new DateTime('now');
    
    $sql = "SELECT start_date, end_date FROM conventions WHERE convention_name = " . $convention_name . " AND convention_number = ". $convention_number;
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0)
    {
        while($row = mysqli_fetch_array($result))
        {
            $con_start_date = $row["start_date"];
            $con_end_date = $row["end_date"];
        }
        $start_time = date_format($start_time,"Y-m-d H:i:s");
        if($start_time < $con_start_date)
        {
            $start_time_error = "Cannot start before the Convention begins (" . $con_start_date . ")";
        }
        else if($start_time < $current_time)
        {
            $start_time_error = "Cannot start before the current time.";
        }
        
        $end_time = date_format($end_time,"Y-m-d H:i:s");
        if($end_time > $con_end_date)
        {
            $end_time_error = "Cannot end after the Convention ends (" . $con_end_date . ")";
        }
        elseif($end_time < $start_time)
        {
            $end_time_error = "End time must be after Start time.";
        }
    }
    else
    {
        $start_time_error = "Could not find matching Convention.";
    }
    
    $sql = "SELECT start_time, end_time, event_name FROM events WHERE convention_name = ". $convention_name ." AND convention_number = " .
                $convention_number . " AND room_number = ". $room_number;
    $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) > 0)
                {
                    while($row = mysqli_fetch_array($result))
                    {
                        
                        if($start_time >= $row["start_time"] && $start_time < $row["end_time"])
                        {
                           $start_time_error = "Time conflicts with ". $row["event_name"]." in the same room, which runs from " . $row["start_time"] . " to ". $row["end_time"];
                        }
                        
                        if($end_time < $row["end_time"] && $end_time > $row["start_time"])
                        {
                            $end_time_error = "Time conflicts with ". $row["event_name"]." in the same room, which runs from " . $row["start_time"] . " to ". $row["end_time"];
                            
                        }
                    }
      }
      $description = test_input($_POST["description"]);
      if(empty($description))
      {
          $description = "No description provided.";
      }
      if(strlen($description) > 400 )
      {
          $description_error = "Description must be fewer than 400 characters.";
      }
      
      $room_number = test_input($_POST["room_number"]);
      
      $sql = "SELECT venui_id FROM conventions WHERE convention_name = ".$convention_name." AND convention_number = ".$convention_number;
      $result = mysqli_query($conn, $sql);
      if(mysqli_num_rows($result) > 0)
      {
          while($row = mysqli_fetch_array($result))
          {
                $venue_id = $row["venue_id"];
          }
      }
      $sql = "SELECT room_number FROM rooms WHERE venue_id = ? AND room_number = ?";
      if($stmt = mysqli_prepare($conn, $sql))
      {
          mysqli_stmt_bind_param($stmt, "is", $venue_id, $room_number);
          
          if(mysqli_stmt_execute($stmt) == 1)
          {
              mysqli_stmt_store_result($stmt);
              if(mysqli_stmt_num_rows($stmt) == 0)
              {
                  $room_number_error = "Invalid Room Number. Please select one from the list.";
              }
          }
          mysqli_stmt_close($stmt);
      }
      else
      {
          echo "Something went wrong checking the database for that room number, please try again.";
      }
      
      $type_count = 0;
      $selected = test_input($_POST['event_type']);
      
          if($selected == "Convention Services")
          {
              $is_type_convention_services = 1;
              $type_count ++;
          }
          if($selected == "Child Event")
          {
              $is_type_child_event = 1;
              $type_count ++;
          }
          if($selected == "Main Event")
          {
              $is_type_main_event = 1;
              $type_count ++;
          }
          if($selected == "Adult Only")
          {
              $is_type_adult_only = 1;
              $type_count ++;
          }
          if($selected == "Shopping Area")
          {
              $is_type_shopping_area = 1;
              $type_count ++;
          }
          if($selected == "Paid Entry")
          {
              $is_type_paid_entry = 1;
              $type_count ++;
          }
          if($selected == "Crafting")
          {
              $is_type_crafting = 1;
              $type_count ++;
          }
          if($selected == "Panel")
          {
              $is_type_panel = 1;
              $type_count ++;
          }
          if($selected == "Cosplay Show")
          {
              $is_type_cosplay_show = 1;
              $type_count ++;
          }
          
      
    
    if($type_count == 0)
    {
        $type_error = "Must select an event type.";
    }
    
    if(empty($event_name_error) && empty($start_time_error) && empty($end_time_error) && empty($description_error) && empty($room_number_error) && empty($type_error) )  {
        
        if($is_type_convention_services == 1)
        {
            $sql = "CALL createConventionServices(?, ?, ?, ?, ?, ?, ?)";
        }
        else
        {
            echo "Failed to Call, bailing";
            $sql = "";
        }
        
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "sssssss", $event_name, $convention_name, $convention_number, $start_time, $end_time,
                $description, $room_number);
            if(mysqli_stmt_execute($stmt)== 1)
            {
     
                $_SESSION["bounce_message"] = ("Successfully added " . $event_name ."!");
                header("location: detailConvention.php");
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
<h1>Add a new Event to <?php echo $_SESSION["lookup_con_name"] . " " . $_SESSION["lookup_con_number"]?></h1>
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

Event Name: <input type = "text" name = "event_name">
<span class = "error">* <?php echo $event_name_error; ?></span>
<br><br>

<label for="start_time">Start Date and Time:</label>
<input type="datetime-local" id="start_time" name="start_time">
<span class = "error">* <?php echo $start_time_error; ?></span>
<br><br>

<label for="end_time">End Date and Time:</label>
<input type="datetime-local" id="end_time" name="end_time">
<span class = "error">* <?php echo $end_time_error; ?></span>
<br><br>

Event Description: <input type = "text" name = "description">
<span class = "error"> <?php echo $description_error; ?></span>
<br><br>

Room Number: <select id = "room_number" name="room_number">
<option>-</option>
<?php 
require "config.php";
$sql = "SELECT  room_number FROM rooms WHERE venue_id =" . $venue_id;
$results = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($results))
{
    echo "<option>$row[room_number]</option>";
}

?>
</select>*
<span class = "error"> <?php echo $room_number_error; ?></span>
<br><br>

Event Type:<select id = "event_type" name="event_type">
<option>-</option>
<option>Convention Services</option>
<option>Child Event</option>
</select>*
<span class = "error"> <?php echo $type_error; ?></span>
<br><br>



<input type = "submit" name = "submit" value = "Submit">

<br><br>
</form>
<a href = "detailConvention.php">Go back to the details for <?php echo $_SESSION["lookup_con_name"] . " " . $_SESSION["lookup_con_number"]?>.</a>

<br>
<a href = "welcome.php">Go back to the welcome page by clicking here.</a>

</body>
</html>