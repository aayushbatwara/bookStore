<?php

session_start();
#Set the access counter
if (isset($_SESSION['counter'])) {
$_SESSION['counter'] += 1; } else {
$_SESSION['counter'] = 1; }

// print_r($_SESSION); // for debug

start();

function start(){
    $conn=mysqli_connect('sophia.cs.hku.hk', 'abatwara', '93211390', 'abatwara') 
    or die ('Error! '.mysqli_connect_error($conn)); // connect to database

    $user = $_POST["inputtedUser"];
    $pwd = $_POST["inputtedPassword"];

    // echo $user . $pwd;

    // check whether the user’s input matches with the username and password stored in the login table
    $query = "SELECT COUNT(1) FROM loginTable WHERE userID = '$user' AND pw = '$pwd'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

    $row = mysqli_fetch_array($result);
    // if yes
    if ($row[0] == 1){
        $_SESSION['username'] = $user; //Store authenticated variable 
        session_write_close(); // free session lock`
        echo "Success<br>";
        mergeCarts($conn,$user);
    }
    else{ // if no
        // display the message “Invalid login, please login again.” (with <h1> tag)
        print "<h4 style='margin:0.5rem'> Invalid login, please login again. </h4>";
    }



    mysqli_free_result($result);
    mysqli_close($conn);  
}

function mergeCarts($conn,$user){
    // INSERT CART FOR DEBUG PURPOSES
    // $query = "INSERT INTO `cartTable`(`cartID`, `bookID`, `userID`, `quantity`) VALUES (NULL,'1','tempuser2021','111')";
    // $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query
    // $query = "INSERT INTO `cartTable`(`cartID`, `bookID`, `userID`, `quantity`) VALUES (NULL,'2','tempuser2021','222')";
    // $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query
    // $query = "INSERT INTO `cartTable`(`cartID`, `bookID`, `userID`, `quantity`) VALUES (NULL,'3','tempuser2021','333')";
    // $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query


    // get data of all cart items of tempuser
    $query = "SELECT * FROM `cartTable` WHERE userID = 'tempuser2021'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query
    
    // while($tempCartRow = mysqli_fetch_array($result)) { // for each item in the temp user's cart
    // $resultArray = mysqli_fetch_array($result);
    foreach ($result as $tempCartRow) {
        echo "B<br>";
        $bookID = $tempCartRow['bookID'];
        $tempCartQuantity = $tempCartRow['quantity'];
        echo "for each item in the temp user's cart <br>";
        echo "book " . $bookID . " x " . $tempCartQuantity . " <br>";

        // see if the user already has an item in the logged-in cart with the same book ID
        $query2 = "SELECT * FROM `cartTable` WHERE userID = '$user' AND bookID = $bookID";
        echo "query2: " . $query2 ." <br>";
        $result2 = mysqli_query($conn,$query2) or die("Error! " . mysql_error($conn)); // Execute query

        $userRow = mysqli_fetch_array($result2);
        
        if ($userRow){ //if yes, then reset the values of cart
            echo "Appending quantity…<br>";
            $cartID = $userRow['cartID'];
            $oldQuantity = $userRow['quantity'];
            echo "old Quantity: ".$oldQuantity . "<br>";
            echo "temp Quantity: ".$tempCartQuantity . "<br>";
            $newQuantity = (int)$tempCartQuantity + (int)$oldQuantity;
            echo "new Quantity: ".$newQuantity . "<br>";
            $query = "UPDATE `cartTable` SET `quantity` = '$newQuantity' WHERE `cartTable`.`cartID` = $cartID";
            echo $query;
            $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

        }
        // if not, then make a new item in the cart table
        else{
            echo "inserting new rows for the logged in user…<br>";
            $query = "INSERT INTO `cartTable`(`cartID`, `bookID`, `userID`, `quantity`) VALUES (NULL,'$bookID','$user','$tempCartQuantity')";
            echo "query: " . $query . "<br>";
            $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query    
        // }   
        }

    }
    echo "deleting items <br>";
    // delete all items from the temp carts
    $query = "DELETE FROM `cartTable` WHERE userID = 'tempuser2021'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query
}

?>
