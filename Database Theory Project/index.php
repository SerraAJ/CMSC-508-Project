<?php
require_once "config.php";
echo "Hello World!";

$sql = "CREATE TABLE if not exists accounts(
    account_username NOT NULL,
    account_password NOT NULL,
    PRIMARY KEY (account_username)
)";
if(mysqli_query($conn, $sql)){
    echo "Table created successfully.";
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html>
<body>

<h1>My first PHP page</h1>



</body>
</html>