<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include '../config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $user_id = $_SESSION["user_id"];
    $image = NULL;

    // Handle image upload
    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $image = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $image;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $message = "Failed to upload image!";
            $messageType = "error";
        }
    }

    // Insert post into database
    $sql = "INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $title, $content, $image);

    if ($stmt->execute()) {
        if ($messageType !== "error") {
            $message = "Post created successfully!";
            $messageType = "success";
        }
    } else {
        $message = "Error: " . $stmt->error;
        $messageType = "error";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Post</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="/php_blog_project/assets/nav.css">
    <link rel="stylesheet" href="/php_blog_project/assets/footer.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">


    <!-- Using a SimpleMDE (Markdown Editor) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    
</head>
    <body class="page-add-post">
        <?php include '../components/navbar.php'; ?>

        <main class="post-form-shell">
            <section class="post-form-container">
                <div class="post-form-header">
                    <h1>Craft a New Post</h1>
                    <p class="post-form-subtitle">Share your story with the community. Markdown formatting is supported for rich storytelling.</p>
                </div>

                <?php if (!empty($message)) : ?>
                    <div class="form-status <?php echo $messageType === 'error' ? 'is-error' : 'is-success'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="add_post.php" enctype="multipart/form-data" class="post-form">
                    <div class="post-form-field">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" placeholder="Give your post a standout title" required>
                    </div>

                    <div class="post-form-field">
                        <label for="editor">Content</label>
                        <textarea name="content" id="editor" rows="10" placeholder="Compose your post using Markdown"></textarea>
                        <span class="field-hint">Tip: Use headings, quotes, and code blocks to add structure.</span>
                    </div>

                    <div class="post-form-field">
                        <label for="image">Cover Image</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="image" name="image" accept="image/*">
                            <span class="file-input-label">Drag &amp; drop or browse an image (optional)</span>
                        </div>
                    </div>

                    <div class="post-form-actions">
                        <button type="submit" class="btn-filled">Publish Post</button>
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
                placeholder: "Start writing your narrative...",
                hideIcons: ["guide", "fullscreen", "side-by-side"]
            });

            form.addEventListener("submit", (event) => {
                if (mde.value().trim() === "") {
                    event.preventDefault();
                    alert("Please enter content before publishing!");
                } else {
                    textarea.value = mde.value();
                }
            });
        });
        </script>

        <?php include '../components/footer.php'; ?>
    </body>
</html>