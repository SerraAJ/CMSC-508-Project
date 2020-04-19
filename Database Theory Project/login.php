<?php

session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: welcome.php");
    exit;
}

require_once "config.php";

$username = $password = $username_error = $password_error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = test_input( $_POST["username"]);
    if( empty( $username))
    {
        $username_error = "Please enter a username.";
    }
    
    $password = test_input($_POST["password"]);
    if (empty( $password))
    {
        $password_error = "Please enter your password.";
    }
    
    if(empty($username_error) && empty($password_error))
    {
        $sql = "SELECT account_username, account_password
                FROM accounts
                WHERE account_username = ?";
        
        
        if($stmt = mysqli_prepare($conn, $sql))
        {
            mysqli_stmt_bind_param($stmt, "s", $username);
            
            if(mysqli_stmt_execute($stmt))
            {
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1)
                {
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                    
                    if(mysqli_stmt_fetch($stmt))
                    {
                        if(password_verify($password, $hashed_password))
                        {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["username"] = $username;
                            header("location: welcome.php");
                            
                        }
                        else
                        {
                            //Password is not valid
                            $password_error = "The password you entered was not valid.";
                        }
                    }
                }
                else
                {
                    //username is not valid
                    $username_error = "No account found with that username.";
                }
                
            }
            else
            {
                echo "Something went wrong with the statement execution. Please try again.";
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

<h1>Log In</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
Username: <input type = "text" name = "username">
<span class = "error">* <?php echo $username_error; ?></span>
<br><br>

Password: <input type = "text" name = "password">
<span class = "error">* <?php  echo $password_error; ?></span>
<br><br>



<input type = "submit" name = "submit" value = "Submit">

</form>

<br><br>
Don't have an account? <a href = "register.php">Register a New Account here.</a>

</body>
</html>