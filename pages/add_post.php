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
        }
    }

    // Insert post into database
    $sql = "INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $title, $content, $image);

    if ($stmt->execute()) {
        $message = "Post created successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Post</title>
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Using a SimpleMDE (Markdown Editor) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
</head>
<?php include '../components/navbar.php'; ?>

<body>
    <h2>Add a New Post</h2>
    <p style="color:green;"><?php echo $message; ?></p>

    <form method="POST" action="add_post.php" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Content (Markdown supported):</label><br>
        <textarea name="content" id="editor" rows="10"></textarea><br><br>

        <label>Upload an image (optional):</label><br>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Publish</button>
    </form>

    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const textarea = document.getElementById("editor");
        const mde = new SimpleMDE({ element: textarea });

        const form = document.querySelector("form");
        form.addEventListener("submit", (e) => {
            if (mde.value().trim() === "") {
                e.preventDefault();
                alert("Please enter content before publishing!");
            } else {
                textarea.value = mde.value(); // sync Markdown to textarea
                console.log("Submitting value:", textarea.value);
            }
        });
    });
    </script>
</body>
<?php include '../components/footer.php'; ?>

</html>