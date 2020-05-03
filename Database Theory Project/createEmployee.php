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
    ssn             CHAR(9) NOT NULL,
    pay_rate        NUMERIC(5,2) NOT NULL,
    INDEX(convention_name, convention_number),
    PRIMARY KEY (person_id),
    FOREIGN KEY (convention_name, convention_number) REFERENCES conventions(convention_name, convention_number)
    
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
$ssn = "";
$pay_rate = "";
$first_name_error = "";
$last_name_error = "";
$phone_number_error = "";
$address_error = "";
$date_of_birth_error = "";
$ssn_error = "";
$pay_rate_error = "";

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
        $address_error = "You must enter the employee's address.";
    }
    elseif(strlen($address) > 400)
    {
        $address_error = "This address is too long; must be 400 or fewer characters.";
    }
    
    $phone_number = test_input($_POST["phone_number"]);
    if(empty($phone_number))
    {
        $phone_number_error = "Please enter a phone number for the employee.";
    }
    else if(!ctype_digit($phone_number))
    {
        $phone_number_error = "Please enter the phone number as a 10 digit number with no other characters.";
    }
    else if(strlen($phone_number) < 10 || strlen($phone_number) > 10)
    {
        $phone_number_error = "Please enter the phone number as a 10 digit number with no other characters.";
    }
    
    $date_of_birth = date("Y-m-d H:i:s", strtotime($_POST["date_of_birth"]));
    
    if(strtotime($date_of_birth) > strtotime('now'))
    {
        $date_of_birth_error = "Date of birth must be before current date.";
    }
    
    $ssn = test_input($_POST["ssn"]);
    if(empty($ssn))
    {
        $ssn_error = "Please enter a SSN for the employee.";
    }
    else if(!ctype_digit($ssn))
    {
        $ssn_error = "Please enter the SSN as a 9 digit number with no other characters.";
    }
    else if(strlen($phone_number) < 9 || strlen($phone_number) > 9)
    {
        $ssn_error = "Please enter the SSN as a 9 digit number with no other characters.";
    }
    
    $pay_rate = test_input($_POST["pay_rate"]);
    if(empty($pay_rate))
    {
        $pay_rate_error = "Please enter an hourly pay rate for the employee.";
    }
    else if(!is_numeric($pay_rate))
    {
        $pay_rate_error = "Please enter the pay rate as a number with a decimal.";
    }
    else if($pay_rate > 999.99)
    {
        $pay_rate_error = "Pay rates must be less than $999.99 an hour.";
    }
    else if ($pay_rate < 0)
    {
        $pay_rate_error = "Pay rates must not be negative.";
    }
    
    
    
    if(empty($first_name_error) && empty($last_name_error) && empty($phone_number_error) && empty($address_error) && 
        empty($date_of_birth_error) && empty($ssn_error) && empty($pay_rate_error))
    {
        $sql = "INSERT INTO employees (first_name, last_name, phone_number, address, date_of_birth, convention_name, convention_number,
                    ssn, pay_rate)
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "ssssssssd", $first_name, $last_name, $phone_number, $address, $date_of_birth,
                                        $_SESSION["lookup_con_name"], $_SESSION["lookup_con_number"], $ssn, $pay_rate);
            if(mysqli_stmt_execute($stmt)== 1)
            {
                $new_id = mysqli_insert_id($conn);
                $_SESSION["bounce_message"] = ("Successfully added " . $first_name . " ". $last_name . " under ID " . $new_id . ".");
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
<h1>Register a new employee for <?php echo $_SESSION["lookup_con_name"] . " " . $_SESSION["lookup_con_number"]?></h1>
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

SSN: <input type = "text" name = "ssn">
<span class = "error">* <?php  echo $ssn_error; ?></span>
<br><br>

Hourly Pay Rate: <input type = "number" min = "0" step = "0.01" max = "999.99" name = "pay_rate">
<span class = "error">* <?php  echo $pay_rate_error; ?></span>
<br><br>

<input type = "submit" name = "submit" value = "Submit">

<br><br>
</form>
<a href = "detailConvention.php">Go back to the details for <?php echo $_SESSION["lookup_con_name"] . " " . $_SESSION["lookup_con_number"]?>.</a>

<br>
<a href = "welcome.php">Go back to the welcome page by clicking here.</a>

</body>
</html>