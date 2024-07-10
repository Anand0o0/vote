<?php include 'header.php'; ?>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'voting_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    foreach ($_POST['candidate'] as $post => $candidate_id) {
        $candidate_id = htmlspecialchars($candidate_id);
        $candidate_post = htmlspecialchars($post);

        $stmt = $conn->prepare("SELECT name FROM candidates WHERE id = ?");
        $stmt->bind_param('i', $candidate_id);
        $stmt->execute();
        $stmt->bind_result($candidate_name);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO votes (user_id, candidate_id, candidate_name, candidate_post) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiss', $user_id, $candidate_id, $candidate_name, $candidate_post);
        $stmt->execute();
        $stmt->close();
    }
    echo "<p>Votes cast successfully!</p>";
}

$posts = $conn->query("SELECT DISTINCT post FROM candidates");
?>
<div id="voting-section" class="container">
    <h2>Cast Your Vote</h2>
    <form method="post" action="vote.php">
        <div id="post-list">
            <?php while ($post = $posts->fetch_assoc()): ?>
                <div class="post">
                    <h3><?php echo htmlspecialchars($post['post']); ?></h3>
                    <?php
                    $stmt = $conn->prepare("SELECT id, name, image_path FROM candidates WHERE post = ?");
                    $stmt->bind_param('s', $post['post']);
                    $stmt->execute();
                    $stmt->bind_result($candidate_id, $candidate_name, $candidate_image);
                    while ($stmt->fetch()): ?>
                        <div class="candidate">
                            <input type="radio" name="candidate[<?php echo htmlspecialchars($post['post']); ?>]" value="<?php echo $candidate_id; ?>" required>
                            <label>
                                <img src="<?php echo htmlspecialchars($candidate_image); ?>" alt="<?php echo htmlspecialchars($candidate_name); ?>" width="50">
                                <?php echo htmlspecialchars($candidate_name); ?>
                            </label>
                        </div>
                    <?php endwhile; ?>
                    <?php $stmt->close(); ?>
                </div>
            <?php endwhile; ?>
        </div>
        <input type="submit" value="Vote">
    </form>
</div>

<div id="chat-section" class="container">
    <h2>Global Chat</h2>
    <div id="chat-messages"></div>
    <form id="chat-form">
        <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off">
        <button type="submit">Send</button>
    </form>
</div>

<?php include 'footer.php'; ?>

<script src="chat.js"></script>
