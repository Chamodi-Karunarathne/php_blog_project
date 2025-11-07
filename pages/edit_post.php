<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"] ?? 'user';
$post_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($post_id <= 0) {
    header("Location: my_posts.php");
    exit;
}

$message = "";
$messageType = "";

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? '');
    $content = trim($_POST["content"] ?? '');
    $image = $post["image"];

    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $newImageName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $newImageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image = $newImageName;
        } else {
            $message = "Failed to upload new image.";
            $messageType = "error";
        }
    }

    $sql = "UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $content, $image, $post_id);

    if ($stmt->execute()) {
        $_SESSION["message"] = "Post updated successfully!";
        $stmt->close();
        header("Location: my_posts.php");
        exit;
    }

    $message = "Error updating post: " . $stmt->error;
    $messageType = "error";
    $stmt->close();

    $post["title"] = $title;
    $post["content"] = $content;
    $post["image"] = $image;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="/php_blog_project/assets/nav.css">
    <link rel="stylesheet" href="/php_blog_project/assets/footer.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
</head>
<body class="page-add-post">
    <?php include '../components/navbar.php'; ?>

    <main class="post-form-shell">
        <section class="post-form-container">
            <div class="post-form-header">
                <h1>Update Your Post</h1>
                <p class="post-form-subtitle">Refine your story and keep your readers in the loop. Markdown formatting remains fully supported.</p>
            </div>

            <?php if (!empty($message)) : ?>
                <div class="form-status <?php echo $messageType === 'error' ? 'is-error' : 'is-success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="post-form">
                <div class="post-form-field">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>

                <div class="post-form-field">
                    <label for="editor">Content</label>
                    <textarea name="content" id="editor" rows="10"><?php echo htmlspecialchars($post['content']); ?></textarea>
                    <span class="field-hint">Tip: Use Markdown to add structure and emphasis.</span>
                </div>

                <div class="post-form-field">
                    <label for="image">Cover Image</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="image" name="image" accept="image/*">
                        <span class="file-input-label">Upload a new cover image (optional)</span>
                    </div>
                    <?php if (!empty($post['image'])): ?>
                        <div class="post-form-preview">
                            <span class="field-hint">Current cover</span>
                            <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Current cover image" class="post-form-preview-image">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="post-form-actions">
                    <button type="submit" class="btn-filled">Save Changes</button>
                </div>
            </form>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const textarea = document.getElementById("editor");
        const form = document.querySelector(".post-form");

        const mde = new SimpleMDE({
            element: textarea,
            autofocus: true,
            spellChecker: false,
            status: false,
            placeholder: "Update your narrative...",
            hideIcons: ["guide", "fullscreen", "side-by-side"]
        });

        form.addEventListener("submit", () => {
            textarea.value = mde.value();
        });
    });
    </script>

    <?php include '../components/footer.php'; ?>
</body>
</html>
