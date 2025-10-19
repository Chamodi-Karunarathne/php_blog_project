<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];
$post_id = intval($_GET["id"] ?? 0);

$message = "";

// Fetch existing post
if ($role === 'admin') {
    $sql = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
} else {
    $sql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    die("Post not found or access denied.");
}

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    // Optional image update
    $image = $post["image"];
    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "../uploads/";
        $image = basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $image);
    }

    $sql = "UPDATE posts SET title=?, content=?, image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $content, $image, $post_id);

    if ($stmt->execute()) {
        $_SESSION["message"] = "Post updated successfully!";
        header("Location: my_posts.php");
        exit;
    } else {
        $message = "Error updating post: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
</head>
<?php include '../components/navbar.php'; ?>

<body>
<h1>Edit Post</h1>
<p style="color:red;"><?php echo $message; ?></p>

<form method="POST" enctype="multipart/form-data">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>

    <label>Content:</label><br>
    <textarea name="content" id="editor" rows="10"><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

    <label>Change Image (optional):</label><br>
    <input type="file" name="image" accept="image/*"><br><br>

    <?php if (!empty($post['image'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" width="200" alt="Post image"><br><br>
    <?php endif; ?>

    <button type="submit">Save Changes</button>
</form>

<!-- Set up the Markdown editor for the textarea -->
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const textarea = document.getElementById("editor");
  const mde = new SimpleMDE({ element: textarea });
  document.querySelector("form").onsubmit = () => {
    textarea.value = mde.value();
  };
});
</script>
</body>
<?php include '../components/footer.php'; ?>

</html>
