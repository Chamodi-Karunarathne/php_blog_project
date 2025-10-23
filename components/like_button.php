<?php
// $post_id must be defined before including this file
include_once __DIR__ . '/../config.php';

// Count likes for this post
$countQuery = $conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id = ?");
$countQuery->bind_param("i", $post_id);
$countQuery->execute();
$count = $countQuery->get_result()->fetch_assoc()['total'] ?? 0;

// Check if user already liked
$liked = false;
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $check = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $check->bind_param("ii", $user_id, $post_id);
    $check->execute();
    $check->store_result();
    $liked = $check->num_rows > 0;
}
?>

<div class="like-section" data-post-id="<?php echo $post_id; ?>">
  <button class="like-btn">
    <span class="heart <?php echo $liked ? 'liked' : ''; ?>">&#x2764;</span>
  </button>
  <span class="like-count"><?php echo $count; ?></span>
</div>
