<?php

// cookie stuff here if needed

start();

function start(){
    // Receive the username and password passed from the login form in the login page
    $conn=mysqli_connect('sophia.cs.hku.hk', 'abatwara', '93211390', 'abatwara') 
    or die ('Error! '.mysqli_connect_error($conn)); // connect to database

    $user = $_POST["inputtedUser"];
    $pwd = $_POST["inputtedPassword"];


    // check whether the user’s input matches with any username stored in the login table
    $query = "SELECT COUNT(1) FROM loginTable WHERE userID = '$user'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

    $row = mysqli_fetch_array($result);
    // if account exists
    if ($row[0] >= 1){
        print "<h4 style='margin:0.5rem'>Account already exists.</h4>";

    }
    else{ // if account is new 
        $query = "INSERT INTO `loginTable`(`userID`, `pw`) VALUES ('$user','$pwd')";
        $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query
    
        // display the message “Invalid login, please login again.” (with <h1> tag)
        print "<h4 style='margin:0.5rem'>Account created! Welcome " . $user . "! </h4>";
    }



    mysqli_free_result($result);
    mysqli_close($conn);  
}

?>
