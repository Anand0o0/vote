<?php include 'header.php' ?>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    
    $conn = new mysqli('localhost', 'root', '', 'voting_system');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, password, is_verified FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $is_verified);
    $stmt->fetch();

    if ($stmt->num_rows == 1 && password_verify($password, $hashed_password)) {
        if ($is_verified === 1) {
            $_SESSION['user_id'] = $id;
            setcookie('user_id', $_SESSION['user_id'], time() + 60); // Cookie expires in 1 minute
            header('Location: vote.php');
        } else {
            echo "Your email isn't verified. Please check your registered Gmail for verification code.";
            header('Location: verify.php');
        }
    } else {
        echo "<p>Invalid username, email, registration number, or password.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
<form id="login-form" method="post" action="login.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Login">
</form>
<?php include 'footer.php' ?>