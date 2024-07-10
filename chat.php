<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'voting_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $message = $conn->real_escape_string($_POST['message']);
    $stmt = $conn->prepare("INSERT INTO chat (user_id, message) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $message);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Message could not be sent']);
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $result = $conn->query("SELECT chat.message, users.username FROM chat JOIN users ON chat.user_id = users.id ORDER BY chat.id DESC");
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode(['status' => 'success', 'messages' => $messages]);
}

$conn->close();
?>
