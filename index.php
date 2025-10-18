<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: pages/login.php");
    exit;
}

$sql = "SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home | Blog</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<h1>All Blog Posts</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
<a href="pages/add_post.php" class="btn">➕ New Post</a> |
<a href="pages/my_posts.php" class="btn">📝 My Posts</a> |
<a href="pages/logout.php" class="btn">🚪 Logout</a>
<hr>

<div class="posts-container">
<?php while ($row = $result->fetch_assoc()): ?>
    <div class="post">
        <h2>
            <a href="pages/view_post.php?id=<?php echo $row['id']; ?>">
                <?php echo htmlspecialchars($row['title']); ?>
            </a>
        </h2>
        <p class="meta">
            Posted by <strong><?php echo htmlspecialchars($row['username']); ?></strong>
            on <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
        </p>

        <div class="markdown-content" 
             data-content="<?php echo htmlspecialchars($row['content'], ENT_QUOTES); ?>">
        </div>

        <?php if (!empty($row['image'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Post image">
        <?php endif; ?>

        <a href="pages/view_post.php?id=<?php echo $row['id']; ?>" class="btn">Read More →</a>
    </div>
<?php endwhile; ?>
</div>

<!-- Markdown Renderer -->
<script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
<script>
  const converter = new showdown.Converter({ emoji: true });
  document.querySelectorAll(".markdown-content").forEach(div => {
      const raw = div.getAttribute("data-content");
      let html = converter.makeHtml(raw);
      if (html.length > 400) html = html.substring(0, 400) + "...";
      div.innerHTML = html;
  });
</script>
</body>
</html>
