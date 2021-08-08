<?php

include 'getBooks.php';

// IF NEED COOKIES OR SESSION DATA:
// session_start();
start();

function start(){
    $conn=mysqli_connect('sophia.cs.hku.hk', 'abatwara', '93211390', 'abatwara') 
    or die ('Error! '.mysqli_connect_error($conn)); // connect to database

    $searchValue = $_POST["searchValue"];
    $keywords = explode(" ",$searchValue);
    $books = array();

    foreach ($keywords as $key => $keyword) {
        
        $query = "SELECT * FROM `bookTable` WHERE bookName LIKE BINARY '%$keyword%' OR author LIKE BINARY '%$keyword%'";
        $result = mysqli_query($conn, $query) or die('Error! '.mysqli_error($conn));

        while($row = mysqli_fetch_array($result)) {
            if (!(in_array($row,$books))){
                array_push($books,$row);
            }
      }
    }

    foreach ($books as $key => $book){
        formatOutput($book,"small");
    }




}



?>
