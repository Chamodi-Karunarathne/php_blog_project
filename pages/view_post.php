<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$post_id = intval($_GET["id"]);

$sql = "SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    die("Post not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<?php include '../components/navbar.php'; ?>

<body>
<hr>

<div class="post">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p class="meta">
        Posted by <strong><?php echo htmlspecialchars($post['username']); ?></strong>
        on <?php echo date('M j, Y \a\t g:i A', strtotime($post['created_at'])); ?>
    </p>

    <?php if (!empty($post['image'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post image">
    <?php endif; ?>

    <div class="markdown-content" 
         data-content="<?php echo htmlspecialchars($post['content'], ENT_QUOTES); ?>">
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
<script>
  const converter = new showdown.Converter({ emoji: true });
  const div = document.querySelector(".markdown-content");
  div.innerHTML = converter.makeHtml(div.getAttribute("data-content"));
</script>
</body>
</html>
