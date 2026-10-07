<?php
session_start();

$servername = "localhost";
$dbusername = "root";   // change if needed
$dbpassword = "";       // change if needed
$dbname = "pestmegye";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET'
    && isset($_GET['username'], $_GET['password'])) {
    $username = $_GET['username'];
    $password = $_GET['password'];
    $stmt = $conn->prepare("SELECT id, username FROM felhasznalok WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: messagetogod.php');
        exit;
    }

    echo "nincs találat";
    $stmt->close();
} elseif (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $address = $_POST['address'] ?? " ";

    $stmt = $conn->prepare("INSERT INTO felhasznalok (email, username, password, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $username, $password, $address);

    if ($stmt->execute()) {
        header('Location: logintogod.html');
        exit;
    }

    echo "nem sikerült a művelet";
    $stmt->close();
}

$conn->close();
?>