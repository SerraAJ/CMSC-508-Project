<?php

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location:index.php");
    exit;
}
require "config.php";
$sql = "CREATE TABLE IF NOT EXISTS attendees(
    person_id          INT NOT NULL UNIQUE AUTO_INCREMENT,
    first_name      VARCHAR(30) NOT NULL,
    last_name       VARCHAR(30) NOT NULL,
    phone_number    CHAR(11) NOT NULL,
    address         VARCHAR(400) NOT NULL,
    date_of_birth   DATE NOT NULL,
    convention_name    VARCHAR(100) NOT NULL,
    convention_number    VARCHAR (4) NOT NULL,
    INDEX(convention_name, convention_number),
    PRIMARY KEY (person_id),
    FOREIGN KEY (convention_name, convention_number) REFERENCES conventions(convention_name, convention_number),
   
)";

if(mysqli_query($conn, $sql)){
    
} else{
    echo "ERROR: Could not  execute $sql. " . mysqli_error($conn);
}

$first_name = "";
$last_name = "";
$phone_number = "";
$address = "";
$date_of_birth = "";
$first_name_error = "";
$last_name_error = "";
$phone_number_error = "";
$address_error = "";
$date_of_birth_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $first_name = test_input($_POST["first_name"]);
    if(empty($first_name))
    {
        $first_name_error = "You must enter a first name.";
    }
    elseif( strlen($first_name) > 30)
    {
        $first_name_error = "This name is too long. Must be 30 or fewer characters.";
    }
    
    $last_name = test_input($_POST["last_name"]);
    if(empty($last_name))
    {
        $last_name_error = "You must enter a surname.";
    }
    elseif( strlen($last_name) > 30)
    {
        $last_name_error = "This name is too long. Must be 30 or fewer characters.";
    }
    
    $address = test_input($_POST["address"]);
    if(empty($address))
    {
        $address_error = "You must enter the attendee's address.";
    }
    elseif(strlen($address) > 400)
    {
        $address_error = "This address is too long; must be 400 or fewer characters.";
    }
    
    $phone_number = test_input($_POST["phone_number"]);
    if(empty($phone_number))
    {
        $phone_number_error = "Please enter a phone number for the location.";
    }
    else if(!ctype_digit($phone_number))
    {
        $phone_number_error = "Please enter the phone number as a 10 digit number with no other characters.";
    }
    else if(strlen($phone_number) < 10 || strlen($phone_number) > 10)
    {
        $phone_number_error = "Please enter the phone number as a 10 digit number with no other characters.";
    }
    
    $date_of_birth = date_format($_POST["date_of_birth"],"Y-m-d H:i:s");
    
    if(strtotime($date_of_birth) > strtotime('now'))
    {
        $date_of_birth_error = "Date of birth must be before current date.";
    }
    
    if(empty($first_name_error) && empty($last_name_error) && empty($phone_number_error) && empty($address_error) && empty($date_of_birth_error))
    {
        $sql = "INSERT INTO attendees (first_name, last_name, phone_number, address, date_of_birth, convention_name, convention_number)
                    VALUES(?, ?, ?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_bind_param($stmt, "sssssss", $first_name, $last_name, $phone_number, $address, $date_of_birth,$_SESSION["lookup_con_name"], $_SESSION["lookup_con_number"]);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<body>
<h1>Register a new attendee for <?php echo $_SESSION["lookup_con_name"] . " " . $_SESSION["lookup_con_number"]?></h1>
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

First Name: <input type = "text" name = "first_name">
<span class = "error">* <?php echo $first_name_error; ?></span>
<br><br>

Last Name: <input type = "text" name = "last_name">
<span class = "error">* <?php echo $last_name_error; ?></span>
<br><br>

Phone Number: <input type = "text" name = "phone_number">
<span class = "error">* <?php  echo $phone_number_error; ?></span>
<br><br>

Address: <input type = "text" name = "address">
<span class = "error">* <?php  echo $address_error; ?></span>
<br><br>

<label for="date_of_birth">Date of Birth:</label>
<input type="date" id="date_of_birth" name="date_of_birth">
<span class = "error">* <?php echo $date_of_birth_error; ?></span>
<br><br>

<input type = "submit" name = "submit" value = "Submit">

<br><br>
</form>
<a href = "detailConvention.php">Go back to the details for <?php echo $_SESSION["lookup_con_name"] . " " . $_SESSION["lookup_con_number"]?>.</a>

<br>
<a href = "welcome.php">Go back to the welcome page by clicking here.</a>

</body>
</html>