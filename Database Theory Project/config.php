<?php
    $conn = mysqli_connect('localhost', 'project_25', 'V00727541', 'project_25');
    
    if($conn = false) 
    {
        die("Error: Could not connect to database." .
            mysqli_connect_error() );
    }
?>