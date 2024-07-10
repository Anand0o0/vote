<?php include 'header.php'; ?>
<?php
session_start();

// Allowing only admins
if (!isset($_SESSION['admin_username']) || !$_SESSION['admin_username']) {
    header('Location: admin_login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'voting_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $post = htmlspecialchars($_POST['post']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $image_path = '';

    // Handling image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    if ($id > 0) {
        if (!empty($image_path)) {
            $stmt = $conn->prepare("UPDATE candidates SET name = ?, post = ?, image_path = ? WHERE id = ?");
            $stmt->bind_param('sssi', $name, $post, $image_path, $id);
        } else {
            $stmt = $conn->prepare("UPDATE candidates SET name = ?, post = ? WHERE id = ?");
            $stmt->bind_param('ssi', $name, $post, $id);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO candidates (name, post, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $post, $image_path);
    }

    if ($stmt->execute()) {
        echo "Candidate successfully " . ($id > 0 ? "updated" : "added") . ".";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        echo "Candidate successfully deleted.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all candidates
$result = $conn->query("SELECT * FROM candidates");
$candidates = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}

// Fetch all users
$result_users = $conn->query("SELECT * FROM users WHERE role='student'");
$users = [];
if ($result_users->num_rows > 0) {
    while ($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>

<div id="admin-dashboard" class="container">
    <h1>Manage Candidates</h1>
    <form method="post" action="admin_dashboard.php" enctype="multipart/form-data">
        <input type="hidden" name="id" id="id" value="">
        <label for="name">Name:</label>
        <select id="name" name="name" required>
            <?php foreach ($users as $user) : ?>
                <option value="<?php echo $user['username']; ?>">
                    <?php echo $user['username'] . ' (' . $user['registration_number'] . ')'; ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <label for="post">Post:</label>
        <select id="post" name="post" required>
            <option value="Class Representative">Class Representative</option>
            <option value="Assistant Class Representative">Assistant Class Representative</option>
        </select><br>
        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*"><br><br>
        <input type="submit" value="Save">
    </form>

    <h2>Candidates List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Post</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidates as $candidate) : ?>
                <tr>
                    <td><?php echo $candidate['id']; ?></td>
                    <td><?php echo $candidate['name']; ?></td>
                    <td><?php echo $candidate['post']; ?></td>
                    <td><?php if ($candidate['image_path']) : ?>
                            <img src="<?php echo $candidate['image_path']; ?>" alt="<?php echo $candidate['name']; ?>" width="100">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);" onclick="editCandidate(<?php echo htmlspecialchars(json_encode($candidate)); ?>)">Edit</a>
                        <a href="admin_dashboard.php?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table> <br><br>
</div>
<div class="buttons">
    <a href="results.php" class="button">Results</a>
    <a href="index.php" class="button">Home</a>
    <a href="vote.php" class="button">Vote</a>
</div>
<script>
    function editCandidate(candidate) {
        document.getElementById('id').value = candidate.id;
        document.getElementById('name').value = candidate.name;
        document.getElementById('post').value = candidate.post;
    }
</script>
<?php include 'footer.php'; ?>
