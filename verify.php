<?php include 'header.php'; ?>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $verification_code = htmlspecialchars($_POST['verification_code']);

    $conn = new mysqli('localhost', 'root', '', 'voting_system');
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND is_verified = 0");
    $stmt->bind_param('s', $verification_code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE verification_token = ?");
        $stmt->bind_param('s', $verification_code);
        $stmt->execute();
        echo "Email verified successfully!";
    } else {
        echo "Invalid verification code or email already verified.";
    }
    header("Location: vote.php");
    $stmt->close();
    $conn->close();
}
?>
<form method="post" action="verify.php">
    <h2>Email Verification</h2>
    <label for="verification_code">Enter Verification Code:</label>
    <input type="text" id="verification_code" name="verification_code" required>
    <input type="submit" value="Verify">
</form>
<?php include 'footer.php'; ?>