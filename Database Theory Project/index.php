<?php
require_once "config.php";
echo "Welcome to the Convention Management Database Homepage!\n";

$sql = "CREATE TABLE if not exists accounts(
    account_username    VARCHAR(30) NOT NULL,
    account_password    VARCHAR (255) NOT NULL,
    PRIMARY KEY (account_username)
)";

if(mysqli_query($conn, $sql)){
    echo "Table created successfully.";
} else{
    echo "ERROR: Could not  execute $sql. " . mysqli_error($conn);
}

?>
<!DOCTYPE html>
<html>
<body>

<h1>Welcome to the Convention Management Database.</h1>
<h2>Please register a new account, or log in.</h2>
<br>
<a href = "register.php">Register a New Account</a>
<br>
<a href = "login.php">Log in to an Existing Account</a>



</body>
</html>