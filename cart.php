<?php


session_start();
start();

function start(){
    $conn=mysqli_connect('sophia.cs.hku.hk', 'abatwara', '93211390', 'abatwara') 
    or die ('Error! '.mysqli_connect_error($conn)); // connect to database

    checkTempCart($conn); // if temp session variable is not set then there should not be cart info in the database


    // if command is add --> call add function
    if ($_POST["command"]=="add") add($conn);
    else if ($_POST["command"]=="showCart") showCart($conn);
    else if ($_POST["command"]=="delete") deleteItem($conn);
    else if ($_POST["command"]=="showCartSummary") showCartSummary($conn);
    else if ($_POST["command"]=="emptyCart") emptyCart($conn);

}

function emptyCart($conn){

    if (isset($_SESSION['username'])){ // if already logged in
        $user = $_SESSION['username'];        
    }
    
    else { // else, user has a temp session
        $user = "tempuser2021"; 
    }
    
    // empty cart
    $query = "DELETE FROM `cartTable` WHERE userID = '$user'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

    echo $result;
}

function deleteItem($conn){
    $cartID = $_POST["cartID"];
    $query = "DELETE FROM `cartTable` WHERE cartID = '$cartID'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

}

function checkTempCart($conn){
    // if temp session variable is not set then there should not be cart info in the database
    if (!(isset($_SESSION['temp']))){
        $query = "DELETE FROM `cartTable` WHERE userID = 'tempuser2021'";
        $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query
    }

}

function showCartSummary($conn){

    if (isset($_SESSION['username'])){ // if already logged in
        $user = $_SESSION['username'];        
    }
    
    else { // else, user has a temp session
        $user = "tempuser2021"; 
    }
    
    // get cart data of user
    $query = "SELECT cartTable.cartID, cartTable.userID, cartTable.bookID, cartTable.quantity, bookTable.bookName, bookTable.price  FROM cartTable INNER JOIN bookTable ON cartTable.bookID=bookTable.bookID WHERE userID = '$user'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

    // format output (test with small option and then make edits as necessary)
    $summedPrice = 0;
    foreach ($result as $key => $cartItem) {
        $bookName = $cartItem["bookName"];
        $price = $cartItem["price"];
        $output = '';
        $totalPrice = $price * $cartItem['quantity'];
        $output.= "<p style='margin: 0rem;'>".$cartItem['quantity']." x $bookName – HK$ $totalPrice</p>";

        echo $output;
        $summedPrice += $price * $cartItem['quantity'];

    }
    $output = "<br>
        <p style='margin: 0rem'>Total price: <b>HK$ $summedPrice</b></p>
        <br>
        <p style='margin: 0rem'>Need to order more? Click <a href='cart.html'>here</a> to change order.</p>
        <input id='confirm' type='submit' value='Confirm' style='margin: 1rem 0rem'>
        <p
    ";

    echo $output;

}

function showCart($conn){

    if (isset($_SESSION['username'])){ // if already logged in
        $user = $_SESSION['username'];        
    }
    
    else if (isset($_SESSION['temp'])){ // if user has already started a temp session by adding to cart before
        $user = "tempuser2021"; 
    }
    else { // if user has not added anything to cart nor have they created a temp session
        // Say empty cart and return
        $output= "<div class='bookContainer'><h3 style='text-align: left'>You have an empty cart! </h3></div>";
        echo $output;
        return;
    }

    // get cart data of user
    $query = "SELECT cartTable.cartID, cartTable.userID, cartTable.bookID, cartTable.quantity, bookTable.bookName, bookTable.price  FROM cartTable INNER JOIN bookTable ON cartTable.bookID=bookTable.bookID WHERE userID = '$user'";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

    // format output (test with small option and then make edits as necessary)
    $summedPrice = 0;
    foreach ($result as $key => $cartItem) {
        $bookName = $cartItem["bookName"];
        $price = $cartItem["price"];

        $imgURL = "upload_image/book_" . $cartItem["bookID"] .  ".jpeg'";
        $output = '';
        $output.= "<div id='".$cartItem['bookID']."' class='bookContainer' style='text-align:center'>";
        $output.= "<a href='".$cartItem["bookID"] . ".html"."'><h3>$bookName</h3> </a>";
        $output.= "<img src='".$imgURL."alt='".$cartItem['bookID']."' style='max-width: 20rem; height: auto;'/>";
        $output.= "<p style='text-align: center'>Quantity: ".$cartItem['quantity']."</p>";
        $output.= "<input type='button' id=". $cartItem["cartID"] ." value='Delete' onclick='deleteItem(this.id)'>";
        $output.= "</div>";

        echo $output;
        $summedPrice += $price * $cartItem['quantity'];

    }
    $output = "<br>
    <hr>
        <div style='text-align: center'>
                <h3>Total price: $summedPrice</h3>
                <input type='button' value='Back' onclick='goBack()'>
                <input type='button' value='Checkout' onclick='goCheckout()'>
        </div>
    ";

    echo $output;



}

function add($conn){
    $bookID = $_POST["bookID"]; 
    $quantity = $_POST["quantity"];

    if (isset($_SESSION['username'])){ // if already logged in
        echo "User already logged in";
        $user = $_SESSION['username'];
        // user will be the username value in session array
        
    }
    
    else if (isset($_SESSION['temp'])){ // if already added to cart w/o logging in 
        echo "Old session";
        $user = "tempuser2021";       
    }
    else { // if new temp session needs to be created
        echo "new session";

        $user = "tempuser2021"; $pwd = "tempuser2021";        
        $_SESSION['temp'] = "true";

        // check if database has an old temp user and password
        $query = "SELECT * FROM `loginTable` WHERE userID = 'tempuser2021'";
        $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

        $row = mysqli_fetch_array($result);
        if ($row){ //if yes, then reset the values of cart
            $query = "DELETE FROM `cartTable` WHERE userID = 'tempuser2021'";
            $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query
        }
        else {// if not, create temp user and password
            $query = "INSERT INTO `loginTable`(`userID`, `pw`) VALUES ('$user','$pwd')";
            $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query
        }
    }

    // see if the user already has an item in the cart with the same book ID
    $query = "SELECT * FROM `cartTable` WHERE userID = '$user' AND bookID = $bookID";
    $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

    $row = mysqli_fetch_array($result);
    
    // if yes, then simply append new quantity
    if ($row){ //if yes, then reset the values of cart
        $cartID = $row['cartID'];
        $oldQuantity = $row['quantity'];
        $quantity = $quantity + (int)$oldQuantity;
        $query = "UPDATE `cartTable` SET `quantity` = '$quantity' WHERE `cartTable`.`cartID` = $cartID";
        echo $query;
        $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query

    }
    // if not, then make a new item in the cart table
    else{
        $query = "INSERT INTO `cartTable`(`cartID`, `bookID`, `userID`, `quantity`) VALUES (NULL,$bookID,'$user',$quantity)";
        $result = mysqli_query($conn,$query) or die("Error! " . mysql_error($conn)); // Execute query    
    }

    session_write_close(); // free session lock (do this when i am done processing the session in this script)

}

mysqli_free_result($result);
mysqli_close($conn);  

?>
