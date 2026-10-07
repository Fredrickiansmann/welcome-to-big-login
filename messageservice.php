<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$servername = "localhost";
$dbusername = "root";   // change if needed
$dbpassword = "";       // change if needed
$dbname = "pestmegye";

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}
$conn->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($userId < 1) {
        http_response_code(401);
        echo json_encode(["error" => "Jelentkezz be az üzenetküldéshez."]);
        $conn->close();
        exit;
    }

    if ($message === '' || strlen($message) > 255) {
        http_response_code(400);
        echo json_encode(["error" => "Az üzenet nem lehet üres, és legfeljebb 255 karakteres lehet."]);
        $conn->close();
        exit;
    }

    $insertStmt = $conn->prepare("INSERT INTO chat (fellhasid, tartalom) VALUES (?, ?)");
    $insertStmt->bind_param("is", $userId, $message);
    if (!$insertStmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Could not save the message."]);
    } else {
        echo json_encode(["ok" => true]);
    }
    $insertStmt->close();
    $conn->close();
    exit;
}

$sql = "SELECT c.fellhasid AS uid, COALESCE(u.username, CONCAT('User #', c.fellhasid)) AS name,
               c.tartalom AS text, c.time AS time
        FROM chat c
        LEFT JOIN felhasznalok u ON u.id = c.fellhasid
        ORDER BY c.time DESC, c.id DESC
        LIMIT 100";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Could not load messages."]);
    $conn->close();
    exit;
}

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
echo json_encode(array_reverse($messages), JSON_UNESCAPED_UNICODE);
$result->free();
$conn->close();
?>