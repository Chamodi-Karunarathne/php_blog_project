<?php
include_once __DIR__ . '/../config.php';

// Fetch top 5 most liked posts
$sql = "SELECT posts.*, users.username, COUNT(likes.id) AS total_likes
        FROM posts
        JOIN users ON posts.user_id = users.id
        LEFT JOIN likes ON posts.id = likes.post_id
        GROUP BY posts.id
        ORDER BY total_likes DESC
        LIMIT 5";
$topLiked = $conn->query($sql);

$isLoggedIn = isset($_SESSION["user_id"]);
?>

<section class="top-liked">
  <h2>🔥 Most Liked Articles</h2>
  <div class="slider">
    <?php while ($post = $topLiked->fetch_assoc()): ?>
      <div class="slide">
        <?php if (!empty($post['image'])): ?>
          <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
        <?php endif; ?>

        <div class="slide-content">
          <h3>
            <a href="<?php echo $isLoggedIn ? 'pages/view_post.php?id=' . $post['id'] : 'pages/login.php'; ?>">
              <?php echo htmlspecialchars($post['title']); ?>
            </a>
          </h3>
          <p>❤️ <?php echo $post['total_likes']; ?> likes</p>
          <p>By <strong><?php echo htmlspecialchars($post['username']); ?></strong></p>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>
