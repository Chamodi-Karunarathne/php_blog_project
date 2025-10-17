<?php
session_start();

if (!isset($_SESSION["user_id"])) {             //Redirects to login page if not logged in
    header("Location: pages/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <a href="pages/add_post.php">Add New Post</a>       <!-- Link to add new post page-->
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION["username"]; ?>!</h1>
    <p>You are logged in as <strong><?php echo $_SESSION["role"]; ?></strong>.</p>
    <a href="pages/logout.php">Logout</a>
</body>
</html>
