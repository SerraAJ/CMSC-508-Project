<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}
require_once "config.php";
$sql = "CREATE TABLE IF NOT EXISTS convention_centers(
    venue_id        INT NOT NULL AUTO_INCREMENT,
    name          VARCHAR(200) NOT NULL,
    address      VARCHAR(999) NOT NULL UNIQUE,
    phone_number CHAR(11) NOT NULL,
    floors       INT(3),
    PRIMARY KEY (venue_id)
)";
if(mysqli_query($conn, $sql)){
    
    
} else{
    echo "ERROR: Could not  execute $sql. " . mysqli_error($conn);
}

$name = "";
$address = "";
$phone_number = "";
$floors = "";
$name_error = "";
$address_error = "";
$phone_number_error = "";
$floors_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $name = test_input($_POST["name"]);
    if(empty($name))
    {
        $name_error = "You must enter a name for the convention center.";
    }
    elseif( strlen($name) > 200)
    {
        $name_error = "This name is too long. Must be 200 or fewer characters.";
    }
    
    $address = test_input($_POST["address"]);
    if(empty($address))
    {
        $address_error = "You must enter the convention center's address.";
    }
    elseif(strlen($address) > 999)
    {
        $address_error = "This address is too long; must be 999 or fewer characters.";
    }
    else
    {
        $sql = "SELECT address FROM convention_centers WHERE address = ?";
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "s", $address);
            
            if(mysqli_stmt_execute($stmt) == 1)
            {
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1)
                {
                    $address_error = "This address is already registered.";
                }
            }
         mysqli_stmt_close($stmt);
        }
        else
        {
            echo "Something went wrong checking the database for that address, please try again.";
        }
    }
    
    $phone_number = test_input($_POST["phone_number"]);
    if(empty($phone_number))
    {
        $phone_number_error = "Please enter a phone number for the location.";
    }
    else if(!is_int($phone_number) || strlen($phone_number) <> 10)
    {
        $phone_number_error = "Please enter the phone number as a 10 digit number with no other characters.";
    }
    
    $floors = test_input($_POST["floors"]); 
    if(!is_int($floors) && !empty($floors))
    {
        $floors_error = "Please enter a number of floors or leave blank.";
    }
    
    if(empty($name_error) && empty($address_error) && empty($phone_number_error) && empty($floors_error))
    {
        $sql = "INSERT INTO convention_centers (name, address, phone_number, floors) VALUES (?, ?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "ssii", $name, $address, $phone_number, $floors);
            if(mysqli_stmt_execute($stmt))
            {
                $new_id = mysqli_insert_id($conn);
                $_SESSION["bounce_message"] = ("Successfully added " . $name . " under ID " . $new_id . ".");
                header("location: welcome.php");
            }
            else 
            {
                echo "Something went wrong executing the statement to add this to the database. Please try again.";
            }
            mysqli_stmt_close($stmt);
            
        }
    }
    my_sqli_close($conn);
    
    
}


?>

<!DOCTYPE html>
<html>
<body>

<h1>Add a new Convention Center or Venue</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
Name of Venue: <input type = "text" name = "name">
<span class = "error">* <?php echo $name_error; ?></span>
<br><br>

Address of Venue: <input type = "text" name = "address">
<span class = "error">* <?php  echo $address_error; ?></span>
<br><br>

Venue's Phone Number: <input type = "text" name = "phone_number">
<span class = "error">* <?php  echo $phone_number_error; ?></span>
<br><br>

Number of Floors in Venue: <input type = "text" name = "floors">
<span class = "error"> <?php  echo $floors_error; ?></span>
<br><br>



<input type = "submit" name = "submit" value = "Submit">

</form>

<br><br>
 <a href = "welcome.php">Go back to the welcome page by clicking here.</a>

</body>
</html>