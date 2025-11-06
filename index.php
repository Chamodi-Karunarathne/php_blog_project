<?php
session_start();
include 'config.php';
//include 'components/top_liked.php';     // Include top liked posts

// Fetch all posts for public view
$sql = "SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);

$isLoggedIn = isset($_SESSION["user_id"]);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Home | Blog</title>
    <link rel="stylesheet" href="/php_blog_project/assets/style.css">
    <link rel="stylesheet" href="/php_blog_project/assets/nav.css">
    <link rel="stylesheet" href="/php_blog_project/assets/footer.css">
</head>
<body>
<?php include 'components/navbar.php'; ?>
<main class="home-layout">

    <div class="main-content">
        <?php include 'components/top_liked.php'; ?>
        <div class="posts-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $hasImage = !empty($row['image']);
                    $postLink = $isLoggedIn
                        ? 'pages/view_post.php?id=' . $row['id']
                        : 'pages/login.php';
                ?>
                <article class="post post-card<?php echo $hasImage ? ' has-cover' : ''; ?>">
                    <?php if ($hasImage): ?>
                        <figure class="post-card__cover">
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        </figure>
                    <?php endif; ?>

                    <header class="post-card__header">
                        <h2>
                            <a href="<?php echo $postLink; ?>">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </a>
                        </h2>
                        <p class="meta">
                            Posted by <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                            on <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                        </p>
                    </header>

                    <div class="post-card__excerpt">
                        <div class="markdown-content" 
                            data-content="<?php echo htmlspecialchars($row['content'], ENT_QUOTES); ?>">
                        </div>
                    </div>

                    <footer class="post-card__footer">
                        <?php $post_id = $row['id']; include 'components/like_button.php'; ?>
                        <a href="<?php echo $postLink; ?>" class="post-card__link">
                            Read Article →
                        </a>
                    </footer>
                </article>
            <?php endwhile; ?>
        </div>
    </div> <!-- end of main-content -->

</main>

<!-- Markdown Rendering -->
<script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
<script>
  const converter = new showdown.Converter({ emoji: true });
  document.querySelectorAll(".markdown-content").forEach(div => {
      const raw = div.getAttribute("data-content");
      let html = converter.makeHtml(raw);
      const maxLength = 180;
      if (html.length > maxLength) {
          html = html.substring(0, maxLength).trimEnd() + "...";
      }
      div.innerHTML = html;
  });
</script>

<?php include 'components/like_script.php'; ?>
<?php include 'components/footer.php'; ?>

</body>
</html>
