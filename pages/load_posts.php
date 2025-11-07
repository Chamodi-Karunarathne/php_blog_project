<?php
session_start();
include '../config.php';

$perPage = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 6;
$page    = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset  = ($page - 1) * $perPage;

$sql = "SELECT posts.*, users.username
        FROM posts
        JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

$countRes = $conn->query("SELECT COUNT(*) AS total FROM posts");
$totalPosts = $countRes ? (int) $countRes->fetch_assoc()['total'] : 0;
$hasMore = ($offset + $perPage) < $totalPosts;

ob_start();
while ($row = $result->fetch_assoc()):
  $isLoggedIn = isset($_SESSION["user_id"]);
  $postLink = $isLoggedIn ? 'pages/view_post.php?id=' . $row['id'] : 'pages/login.php';
  $hasImage = !empty($row['image']);
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
    <div class="markdown-content" data-content="<?php echo htmlspecialchars($row['content'], ENT_QUOTES); ?>"></div>
  </div>

  <footer class="post-card__footer">
    <?php $post_id = $row['id']; include '../components/like_button.php'; ?>
    <a href="<?php echo $postLink; ?>" class="post-card__link">
      Read Article →
    </a>
  </footer>
</article>
<?php endwhile; ?>
<div data-has-more="<?php echo $hasMore ? 'true' : 'false'; ?>"></div>
<?php
echo ob_get_clean();
