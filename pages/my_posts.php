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
    <link rel="stylesheet" href="/php_blog_project/assets/style.css">
    <link rel="stylesheet" href="/php_blog_project/assets/nav.css">
    <link rel="stylesheet" href="/php_blog_project/assets/footer.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">

</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <main class="home-layout">
      <div class="main-content">
        <header>
          <?php if (isset($_SESSION["message"])): ?>
            <p class="success-msg"><?php echo $_SESSION["message"]; unset($_SESSION["message"]); ?></p>
          <?php endif; ?>
        </header>

        <?php if ($result->num_rows > 0): ?>
          <section class="posts-container">
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="post">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <div class="markdown-content" 
                    data-content="<?php echo htmlspecialchars($row['content'], ENT_QUOTES); ?>">
                </div>
                <?php if (!empty($row['image'])): ?>
                  <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Post image">
                <?php endif; ?>
                <p class="meta">
                    Posted by<b> <?php echo htmlspecialchars($row["username"]); ?> </b>
                    on <?php echo $row["created_at"]; ?>
                </p>
                <div class="actions">
                    <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="action-btn">Edit</a>
                    <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="action-btn delete-action" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                </div>
              </div>
            <?php endwhile; ?>
          </section>
        <?php else: ?>
          <p>You haven’t created any posts yet.</p>
        <?php endif; ?>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
    <script>
        const converter = new showdown.Converter({ emoji: true });
        document.querySelectorAll(".markdown-content").forEach(div => {
            const raw = div.getAttribute("data-content");
            let html = converter.makeHtml(raw);
            if (html.length > 400) {
                html = html.substring(0, 400) + "...";
            }
            div.innerHTML = html;
        });
    </script>
    <?php include '../components/footer.php'; ?>
</body>
</html>
