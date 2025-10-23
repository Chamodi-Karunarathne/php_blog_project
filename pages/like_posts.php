<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Check if user already liked this post
$check = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
$check->bind_param("ii", $user_id, $post_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    // Unlike
    $conn->query("DELETE FROM likes WHERE user_id = $user_id AND post_id = $post_id");
    $liked = false;
} else {
    // Like
    $conn->query("INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)");
    $liked = true;
}

// Return updated like count
$countRes = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE post_id = $post_id");
$totalLikes = $countRes->fetch_assoc()['total'];

echo json_encode(['success' => true, 'liked' => $liked, 'total_likes' => $totalLikes]);
