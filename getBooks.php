<?php
  
$conn=mysqli_connect('sophia.cs.hku.hk', 'abatwara', '93211390', 'abatwara') or die ('Error! '.mysqli_connect_error($conn));

function formatOutput ($row,$size) {
    if ($size == "big") {
        $imgURL = "upload_image/book_" . $row["bookID"] .  ".jpeg'";
        $output = '';
        $output.= "<div id='".$row['bookID']."' class='bookContainer singleBook'>";
        $output.= "<h3 style='text-align: left; display: none'>".$book = $row["bookName"]."</h3>";
        $output.= "<img src='".$imgURL."alt='".$row['bookName']."' style='max-width: 20rem; height: auto;'/>";
        $output.= "<p><b>Author:</b> " . $row['author']." </p>";
        $output.= "<p><b>Published:</b> " . $row['published']." </p>";
        $output.= "<p><b>Publisher:</b> ". $row['publisher']."</p>";
        $output.= "<p><b>Category:</b> ". $row['category']."</p>";
        $output.= "<p><b>Language:</b> ". $row['lang']."</p>";
        $output.= "<p><b>Description:</b> ". $row['description']."</p>";
        $output.= "<p><b>Price:</b> ".$row['price']."</p>";
        $output.= '<label for="quantity"><b>Order Quantity:<b></label>';
        $output.= '<input type="number" id="quantity" value="1" name="quantity" min="1">';
        $output.= '<input type="button" onclick="addToCart()" value="Add to Cart">';
        $output.="</div>";

        echo $output;          
    }
    else if ($size == "small") {
        $imgURL = "upload_image/book_" . $row["bookID"] .  ".jpeg'";
        $newArrival = "";
        if ($row["newArrival"] == "Yes") {
            $newArrival = '<p style="font-weight: bold;">New Arrival!</p>';
        }
        $output = '';
        $output.= "<div id='".$row['bookID']."' class='bookContainer'>";
        $output.= "<a href='".$row["bookID"] . ".html"."'><h3>".$book = $row["bookName"]."</h3> </a>";
        $output.= "<img src='".$imgURL."alt='".$row['bookName']."' style='max-width: 20rem; height: auto;'/>";
        $output.= $newArrival;
        $output.= "<p>Author: " . $row['author']." </p>";
        $output.= "<p>Publisher: ". $row['publisher']."</p>";
        $output.= "<p>Price: ".$row['price']."</p>";
        $output.="</div>";

        echo $output;          
    }
}

$size = "small";
if (is_numeric($_POST['show'])){ // if individual page number
    $id = $_POST['show'];
    $query = "SELECT * FROM bookTable WHERE bookID = $id";
    $size = "big";
}
else if ($_POST['show'] == 'all') {
    $query = 'select * from bookTable'; //[Construct SQL query] select all entries from attendancelist table
}
else{
    $category = $_POST['show'];
    $query = "SELECT * FROM bookTable WHERE category = '$category'"; //ENTER CONDITION
}

if ($_POST['sort'] == "true"){ // if books are to be sorted
    $query .= ' ORDER BY price DESC';
}

$result =  mysqli_query($conn, $query) or die('Error! '. mysql_error($conn)); //Execute SQL query

// generate HTML code that displays entries
while($row = mysqli_fetch_array($result)) {
    formatOutput($row,$size);
}

 


  mysqli_free_result($result);
  mysqli_close($conn);    
  

?>