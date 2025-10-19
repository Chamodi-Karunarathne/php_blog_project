<?php
session_start();
include '../config.php';

//Redirects to login page if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

// Admins can view all posts, Users see only their own
if ($role === 'admin') {
    $sql = "SELECT posts.*, users.username 
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            ORDER BY posts.created_at DESC";
    
    $result = $conn->query($sql); // direct query, no placeholders
} else {
    $sql = "SELECT posts.*, users.username
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.user_id = ?
            ORDER BY posts.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Posts</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .post {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        .actions {
            margin-top: 10px;
        }
        .actions a {
            text-decoration: none;
            margin-right: 10px;
            color: #007bff;
        }
        .actions a.delete {
            color: red;
        }
    </style>
</head>
<?php include '../components/navbar.php'; ?>
<body>
    <h2>My Posts</h2>
    <?php if (isset($_SESSION["message"])): ?>
        <p style="color:green;"><?php echo $_SESSION["message"]; unset($_SESSION["message"]); ?></p>
    <?php endif; ?>


    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <div class="markdown-content" 
                    data-content="<?php echo htmlspecialchars($row['content'], ENT_QUOTES); ?>">
                </div>
                <p class="meta">
                    Posted by <?php echo htmlspecialchars($row["username"]); ?> 
                    on <?php echo $row["created_at"]; ?>
                </p>

                <div class="actions">
                    <a href="edit_post.php?id=<?php echo $row['id']; ?>">Edit</a>
                    <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You haven’t created any posts yet.</p>
    <?php endif; ?>
    <!-- Include Showdown.js for Markdown rendering -->
    <script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
    <script>
        const converter = new showdown.Converter({ emoji: true });
        document.querySelectorAll(".markdown-content").forEach(div => {
            const raw = div.getAttribute("data-content");
            let html = converter.makeHtml(raw);

            // Show only the first 100 characters of Markdown as preview
            if (html.length > 400) {
                html = html.substring(0, 400) + "...";
            }

            div.innerHTML = html;
        });
    </script>

</body>
<?php include '../components/footer.php'; ?>

</html>
