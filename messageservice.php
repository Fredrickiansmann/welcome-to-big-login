<?php
$servername = "localhost";
$dbusername = "root";   // change if needed
$dbpassword = "";       // change if needed
$dbname = "pestmegye";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM chat";
// Execute the query
if(!($chatresult = mysqli_query($conn, $sql))){
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
}

    if(isset($_POST['userid']) and isset($_POST['message']))){
    $userid = $_POST['userid'];
    $message = $_POST['message'];
    $sql = "INSERT INTO `chat` ( `fellhasid`, `tartalom`) VALUES
    ('$userid' ,'$message')";
    if(!($result = mysqli_query($conn, $sql))){
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
    }else{
        header('Location: logintogod.html');
    }
    mysqli_close($conn);
    
    

}
?>