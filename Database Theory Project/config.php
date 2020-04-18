<?php
    $conn = mysqli_connect('localhost', 'root', '');
    
    if($conn = false) 
    {
        die("Error: Could not connect to database." .
            mysqli_connect_error() );
    }
?>