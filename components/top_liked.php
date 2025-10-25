<?php
include_once __DIR__ . '/../config.php';

$sql = "SELECT posts.*, users.username, COUNT(likes.id) AS total_likes
        FROM posts
        JOIN users ON posts.user_id = users.id
        LEFT JOIN likes ON posts.id = likes.post_id
        GROUP BY posts.id
        ORDER BY total_likes DESC
        LIMIT 5";
$topLiked   = $conn->query($sql);
$isLoggedIn = isset($_SESSION["user_id"]);
?>

<section class="top-liked">
  <div class="top-liked__header">
    <span class="top-liked__label">Top picks this week</span>
    <h2>Most Liked Articles</h2>
  </div>

  <div class="slider" role="list">
    <?php while ($post = $topLiked->fetch_assoc()): ?>
      <?php
        $link     = $isLoggedIn ? 'pages/view_post.php?id=' . $post['id'] : 'pages/login.php';
        $hasImage = !empty($post['image']);
        $excerpt  = trim(strip_tags($post['content']));
        if (function_exists('mb_strlen')) {
            if (mb_strlen($excerpt) > 160) {
                $excerpt = mb_substr($excerpt, 0, 160) . '…';
            }
        } else {
            if (strlen($excerpt) > 160) {
                $excerpt = substr($excerpt, 0, 160) . '…';
            }
        }
        $postedAt = !empty($post['created_at'])
          ? date('F j, Y', strtotime($post['created_at']))
          : '';
      ?>
      <article class="slide<?php echo $hasImage ? '' : ' no-image'; ?>" role="listitem">
        <div class="slide-info">
          <span class="pill">Most liked</span>
          <h3>
            <a href="<?php echo $link; ?>">
              <?php echo htmlspecialchars($post['title']); ?>
            </a>
          </h3>
          <?php if ($excerpt): ?>
            <p class="slide-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>
          <?php endif; ?>
          <div class="slide-meta">
            <span class="slide-author"><?php echo htmlspecialchars($post['username']); ?></span>
            <span class="slide-separator">•</span>
            <span class="slide-date"><?php echo htmlspecialchars($postedAt); ?></span>
          </div>
          <div class="slide-footer">
            <span class="slide-likes-chip">
              <span class="slide-heart">&#x2764;</span>
              <?php echo $post['total_likes']; ?> likes
            </span>
            <a class="slide-link" href="<?php echo $link; ?>">Read article →</a>
          </div>
        </div>

        <?php if ($hasImage): ?>
          <figure class="slide-cover">
            <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
          </figure>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  </div>
</section>