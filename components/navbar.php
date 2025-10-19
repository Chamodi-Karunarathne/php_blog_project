<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION["user_id"]);
?>

<header class="navbar">
  <div class="nav-left">
    <span class="logo">BlogCave</span>

    <a href="/php_blog_project/index.php" class="nav-link">Home</a>
    <?php if ($isLoggedIn): ?>
      <a href="/php_blog_project/pages/my_posts.php" class="nav-link">My Posts</a>
      <a href="/php_blog_project/pages/add_post.php" class="nav-link">New Post</a>
    <?php endif; ?>
  </div>

  <div class="nav-right">
    <?php if (!$isLoggedIn): ?>
      <a href="/php_blog_project/pages/login.php" class="btn-outline">Sign In</a>
      <a href="/php_blog_project/pages/register.php" class="btn-filled">Get Started</a>
    <?php else: ?>
      <a href="/php_blog_project/pages/logout.php" class="btn-logout">Logout</a>
      <a href="/php_blog_project/pages/profile.php" class="nav-profile-link">
        <div class="nav-profile-pic">
          <img src="<?php 
              if (!empty($_SESSION['profile_image'])) {
                  echo '/php_blog_project/uploads/' . htmlspecialchars($_SESSION['profile_image']);
              } else {
                  echo '/php_blog_project/assets/default-avatar.png';
              }
          ?>" alt="Profile">
        </div>
      </a>
    <?php endif; ?>
  </div>
</header>
