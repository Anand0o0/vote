<?php
if (!isset($_GET['post'])) {
    http_response_code(400);
    die("Error: Post parameter is missing.");
}

$conn = new mysqli('localhost', 'root', '', 'voting_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$post = htmlspecialchars($_GET['post']);
$stmt = $conn->prepare("SELECT id, name, image_path FROM candidates WHERE post = ?");
$stmt->bind_param('s', $post);
$stmt->execute();
$result = $stmt->get_result();

$candidates = [];
while ($row = $result->fetch_assoc()) {
    $candidates[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode($candidates);
?>