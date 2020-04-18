<?php
require_once "config.php";
echo "Hello World!";

$sql = "CREATE TABLE accounts(
    account_username NOT NULL PRIMARY KEY,
    account_password NOT NULL
)";
if(mysqli_query($conn, $sql)){
    echo "Table created successfully.";
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
?>
<!DOCTYPE html>
<html>
<body>

<h1>My first PHP page</h1>



</body>
</html>