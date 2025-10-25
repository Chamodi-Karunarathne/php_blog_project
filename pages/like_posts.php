<?php
session_start();
include '../config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

// Check if already liked
$check = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
$check->bind_param("ii", $user_id, $post_id);
$check->execute();
$res = $check->get_result();

if ($res && $res->num_rows > 0) {
    // Unlike
    $del = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $del->bind_param("ii", $user_id, $post_id);
    if (!$del->execute()) {
        echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $del->error]);
        exit;
    }
    $liked = false;
} else {
    // Like
    $ins = $conn->prepare("INSERT IGNORE INTO likes (user_id, post_id) VALUES (?, ?)");
    $ins->bind_param("ii", $user_id, $post_id);
    if (!$ins->execute()) {
        echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $ins->error]);
        exit;
    }
    $liked = true;
}

// Count total likes
$countRes = $conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id = ?");
$countRes->bind_param("i", $post_id);
$countRes->execute();
$totalLikes = $countRes->get_result()->fetch_assoc()['total'] ?? 0;

echo json_encode(['success' => true, 'liked' => $liked, 'total_likes' => $totalLikes]);
?>