<?php

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}
require "config.php";
$sql = "CREATE TABLE IF NOT EXISTS conventions(
    convention_name    VARCHAR(100) NOT NULL,
    convention_number    VARCHAR (4) NOT NULL,
    start_date         DATE NOT NULL,
    end_date            DATE NOT NULL,
    convention_type     VARCHAR(50) DEFAULT 'None',
    convention_theme    VARCHAR(50) DEFAULT 'None',
    venue_id            INT NOT NULL,
    PRIMARY KEY (convention_name, convention_number),
    FOREIGN KEY (venue_id) REFERENCES convention_centers(venue_id)
)";

if(mysqli_query($conn, $sql)){
    
} else{
    echo "ERROR: Could not  execute $sql. " . mysqli_error($conn);
}

$convention_name = "";
$convention_number = "";
$start_date = "";
$end_date = "";
$convention_type = "";
$convention_theme = "";
$venue_id = "";
$convention_name_error = "";
$convention_number_error = "";
$start_date_error = "";
$end_date_error = "";
$convention_type_error = "";
$convention_theme_error = "";
$venue_id_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $convention_name = test_input($_POST["convention_name"]);
    if(empty($convention_name))
    {
        $convention_name_error = "You must enter a name for the convention.";
    }
    elseif(strlen($convention_name) > 100)
    {
        $convention_name_error = "This name is too long. Must be 100 or fewer characters.";
    }
    
    $convention_number = test_input($_POST["convention_number"]);
    if(empty($convention_number))
    {
        $convention_number_error = "Please enter the convention's number.";
    }
    else
    {
        $sql = "SELECT convention_name FROM conventions WHERE convention_name = ? AND convention_number = ?";
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "ss", $convention_name, $convention_number);
            if(mysqli_stmt_execute($stmt) == 1)
            {
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1)
                {
                    $convention_number_error = "There is already a convention with this name and number. Please review and change one or the other.";
                }
            }
            mysqli_stmt_close($stmt);
        }
        else
        {
            echo "Something went wrong checking the database. Please try again.";
        }
    
    }
    $start_date = new DateTime($_POST["start_date"]);
        $current_time = new DateTime('now');
        if($start_date < $current_time)
       {
          $start_date_error = "Your start date must not be in the past.";
       }
    
    
    $start_date = date_format($start_date,"Y-m-d H:i:s");
    
    $end_date = new DateTime($_POST["end_date"]);
   
    
        if($end_date < $start_date)
        {
            $end_date_error = "The end date must be after the start date.";
        }
    
    $end_date = date_format($end_date,"Y-m-d H:i:s");
    
    $convention_type = test_input($_POST["convention_type"]);
    if(empty($convention_type))
    {
        $convention_type = "None";
    }
    if(strlen($convention_type) > 50 )
    {
        $convention_type_error = "Convention type must be fewer than 50 characters.";
    }
    
    $convention_theme = test_input($_POST["convention_theme"]);
    if(empty($convention_theme))
    {
        $convention_theme = "None";
    }
    if(strlen($convention_theme) > 50 )
    {
        $convention_theme_error = "Convention theme must be fewer than 50 characters.";
    }
    
    $venue_id = test_input($_POST["venue_id"]);
    $venue_id = substr( $venue_id, (strrpos($venue_id, " ")));
    $sql = "SELECT venue_id FROM convention_centers WHERE venue_id = ?";
    if($stmt = mysqli_prepare($conn, $sql))
    {
        mysqli_stmt_bind_param($stmt, "s", $venue_id);
        
        if(mysqli_stmt_execute($stmt) == 1)
        {
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 0)
            {
                $venue_id_error = "Invalid Venue ID. Please select one from the list.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    else
    {
        echo "Something went wrong checking the database for that venue, please try again.";
    }
    
    
    if(empty($convention_name_error) && empty($convention_number_error) && empty($start_date_error) && empty($end_date_error) 
        && empty($convention_type_error) && empty($convention_theme_error) && empty($venue_id_error))
    {
        $sql = "INSERT INTO conventions (convention_name, convention_number, start_date, end_date, convention_type, convention_theme, venue_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)"; 
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "ssssssi", $convention_name, $convention_number, $start_date, $end_date, $convention_type, $convention_theme, $venue_id);
            if(mysqli_stmt_execute($stmt))
            {
                $_SESSION["bounce_message"] = ("Sucessfully added " . $convention_name . " " . $convention_number . "!");
                header("location: welcome.php");
            }
            else
            {
                echo "Something went wrong adding this to the database. Please try again.";
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
<h1>Add a new Convention</h1>
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

Name of Convention: <input type = "text" name = "convention_name">
<span class = "error">* <?php echo $convention_name_error; ?></span>
<br><br>

Convention Number: <input type = "text" name = "convention_number">
<span class = "error">* <?php echo $convention_number_error; ?></span>
<br><br>

<label for="start_date">Start Date and Time:</label>
<input type="datetime-local" id="start_date" name="start_date">
<span class = "error">* <?php echo $start_date_error; ?></span>
<br><br>

<label for="end_date">End Date and Time:</label>
<input type="datetime-local" id="end_date" name="end_date">
<span class = "error">* <?php echo $end_date_error; ?></span>
<br><br>

Convention Type: <input type = "text" name = "convention_type">
<span class = "error"> <?php echo $convention_type_error; ?></span>
<br><br>

Convention Theme: <input type = "text" name = "convention_theme">
<span class = "error"> <?php echo $convention_theme_error; ?></span>
<br><br>

Venue: <select id = "venue_id" name="venue_id">
<option>-</option>
<?php 
require "config.php";
$sql = "SELECT  venue_id, name FROM convention_centers";
$results = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($results))
{
    echo "<option> $row[name]  $row[venue_id]</option>";
}

?>
</select>*
<span class = "error"> <?php echo $venue_id_error; ?></span>
<br><br>
<input type = "submit" name = "submit" value = "Submit">

<br><br>
</form>

<a href = "welcome.php">Go back to the welcome page by clicking here.</a>
</body>
</html>