<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION["user_id"]);
?>

<header class="navbar">
  <div class="nav-left">
    <span class="logo">BlogCave</span>

    <a href="/php_blog_project/index.php" style="text-decoration:none" class="nav-link">Home</a>
    <a href="/php_blog_project/pages/my_posts.php" style="text-decoration:none" class="nav-link">My Posts</a>
    <a href="/php_blog_project/pages/add_post.php" style="text-decoration:none" class="nav-link">New Post</a>
  </div>

  <div class="nav-right">
  <?php if (!isset($_SESSION["user_id"])): ?>
    <a href="login.php" class="btn-outline">Sign In</a>
    <a href="register.php" class="btn-filled">Get Started</a>
  <?php else: ?>
    <a href="pages/logout.php" style="text-decoration:none" class="btn-logout">Logout</a>
  <?php endif; ?>
</div>
</header>
 