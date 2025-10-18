<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

// Get post ID from URL
if (isset($_GET["id"])) {
    $post_id = intval($_GET["id"]);

    // Check whether this user owns the post or is admin
    if ($role === 'admin') {
        $sql = "DELETE FROM posts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $post_id);
    } else {
        $sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $post_id, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION["message"] = "Post deleted successfully!";
    } else {
        $_SESSION["message"] = "Error deleting post: " . $stmt->error;
    }
    $stmt->close();
}

header("Location: my_posts.php");
exit;
?>
