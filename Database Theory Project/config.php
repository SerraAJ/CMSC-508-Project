<?php
    $conn = mysqli_connect('localhost', 'project_25', 'V00727541', 'project_25')
    or
        die("Error: Could not connect to database." .
            mysqli_connect_error() );
    
        
        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
    
?>