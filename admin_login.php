<?php include 'header.php'; ?>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_username = htmlspecialchars($_POST['admin_username']);
    $admin_password = htmlspecialchars($_POST['admin_password']);
$is_verified=0;
    $conn = new mysqli('localhost', 'root', '', 'voting_system');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT password, is_verified FROM users WHERE username = ? AND role = 'admin'");
    $stmt->bind_param('s', $admin_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $is_verified);
        $stmt->fetch();

        if (password_verify($admin_password, $hashed_password)) {
            if ($is_verified === 1) {
                $_SESSION['admin_username'] = $admin_username;
                setcookie('user_id', $id, time() + 60, '/'); // Cookie expires in 1 min. setcookie($name, $value, $expire, '/');
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "Your email isn't verified. Please check your registered Gmail for verification code.";
                header('Location: verify.php');
            }
        } else {
            echo "<p>Invalid password. Please try again.</p>";
        }
    } else {
        echo "<p>Invalid username or you are not an admin.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<h2>Admin Login</h2>
<form id="admin-login-form" method="post" action="admin_login.php">
    <label for="admin_username">Username:</label>
    <input type="text" id="admin_username" name="admin_username" required><br>

    <label for="admin_password">Password:</label>
    <input type="password" id="admin_password" name="admin_password" required><br>

    <input type="submit" value="Login">
</form>
<?php include 'footer.php'; ?>