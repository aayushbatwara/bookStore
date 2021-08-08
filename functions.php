<?php

session_start();
start();

function start(){
    $targetFunction = $_POST["function"];
    
    if ($targetFunction == "checkLoggedIn") checkLoggedIn();
    else if ($targetFunction == "checkLoggedInBoolean") checkLoggedInBoolean();
    else if ($targetFunction == "checkDuplicateUser") checkDuplicateUser();
}

function checkDuplicateUser(){
    $user = $_POST["username"]; //this is the user to check

    $conn=mysqli_connect('sophia.cs.hku.hk', 'abatwara', '93211390', 'abatwara') 
    or die ('Error! '.mysqli_connect_error($conn)); // connect to database

    // check whether the user’s input matches with any username stored in the login table
    $query = "SELECT COUNT(1) FROM loginTable WHERE userID = '$user'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

    $row = mysqli_fetch_array($result);
    // if account exists
    if ($row[0] >= 1){
        echo "true";
    }
    else{ // if account is new     
        echo "false";
    }

    mysqli_free_result($result);
    mysqli_close($conn);  



}

function checkLoggedInBoolean(){
    if (isset($_SESSION['username'])) echo true;
    else echo false;
}

function checkLoggedIn(){
    $cartQty = checkCartQty();
    if (isset($_SESSION['username'])){
        $firstLine = "<input type=\"button\" value=\"Logout\" onclick=\"{ document.getElementById('buttons').innerHTML = '<h2 style=\'text-align: right; \'>Logging out…</h2>'; setTimeout(function(){window.location='logout.php';},3000);}\">";
        print $firstLine;
        print '
        <input type="button" onclick="window.location=\'cart.html\'" style="margin-right: 0rem" value="Cart">
        <sup id="cartQty" style="margin: 0rem;">
        ';
        print $cartQty;
        print'</sup>';

    }
    else{
        print '
        <input type="button" onclick="window.location=\'login.html\'" value="Sign in">
        <input type="button" onclick="window.location=\'register.html\'" value="Register">
        <input type="button" onclick="window.location=\'cart.html\'" style="margin-right: 0rem" value="Cart">
        <sup id="cartQty" style="margin: 0rem;">';
        print $cartQty;
        print'</sup>';
    }
}

function checkCartQty(){
    $conn=mysqli_connect('sophia.cs.hku.hk', 'abatwara', '93211390', 'abatwara') 
    or die ('Error! '.mysqli_connect_error($conn)); // connect to database

    if (isset($_SESSION['username'])){ // if already logged in
        $user = $_SESSION['username'];        
    }
    
    else if (isset($_SESSION['temp'])){ // if user has already started a temp session by adding to cart before
        $user = "tempuser2021"; 
    }
    else { // if user has not added anything to cart nor have they created a temp session
        return 0;
    }
    
    // get cart data of user
    $query = "SELECT cartTable.quantity FROM cartTable WHERE userID = '$user'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

    // format output (test with small option and then make edits as necessary)
    $summedQty = 0;
    foreach ($result as $key => $cartItem) {
        $summedQty += $cartItem['quantity'];
    }
    
    return $summedQty;

    mysqli_free_result($result);
    mysqli_close($conn);  

}

?>
