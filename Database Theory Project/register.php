<?php
require_once "config.php";

$username = "";
$password = "";
$confirm_password = "";

$username_error = "";
$password_error = "";
$confirm_password_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = test_input($_POST["username"]);
    if( empty( $username))
    {
        $username_error = "You have not entered a username.";
    }
    
    elseif( strlen( $username) > 30)
    {
        $password_error = "Username is too long! Please make it fewer than 30 characters.";
    }
    
    else 
    {
        $sql = "SELECT username FROM accounts WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt) == 1)
            {
                $username_error = "This username is already taken.";
            }
            
            mysqli_stmt_close($stmt);
            
        }
        else
        {
            echo "Something went wrong, please try again.";
        }
        
     }
     
     //Password
     $password = test_input($_POST["password"]);
     
     if( empty( $password))
     {
         $password_error = "Please enter a password.";
     }
     
     elseif( strlen( $password) < 8)
     {
         $password_error = "Password is too short! Please make it at least 8 characters long.";
     }
     
     
     //Password Confirmation
     $confirm_password = test_input($_POST["confirm_password"]);
     if( empty( $confirm_password))
     {
         $confirm_password_error = "Please enter your password again to confirm it.";
     }
     
     else 
     {
         if( empty($password_error && $password != $confirm_password))
         {
             $confirm_password_error = "Passwords did not match.";
         }
     }
     
     if( empty($username_error) && empty($password_error) && empty($confirm_password_error))
     {
        $sql = "INSERT INTO accounts (username, password) VALUES (?, ?)";
        if( $stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
        
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if(mysqli_stmt_execute($stmt))
            {
                header("location: index.php");
            }
            else
            {
                echo "Something went wrong adding you to the database. Please try again.";
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

<h1>Create Account</h1>
<h2>Please fill out this form to create an account.</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
Username: <input type = "text" name = "username">
<span class = "error">* <?php echo $username_error; ?></span>
<br><br>

Password: <input type = "text" name = "password">
<span class = "error">* <?php  echo $password_error; ?></span>
<br><br>

Confirm Password: <input type = "text" name = "confirm_password">
<span class = "error">* <?php  echo $confirm_password_error; ?></span>
<br><br>

<input type = "submit" name = "submit" value = "Submit">

</form>



</body>
</html>