<?php
session_start();
include 'config.php';   //Needed to run SQL queries

//Redirects to login page if not logged in
if (!isset($_SESSION["user_id"])) {             
    header("Location: pages/login.php");
    exit;
}

//Fetch all the posts with author info
$sql = "SELECT posts.*, users.username        
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 20px;
        }
        h1 { color: #333; }
        a { text-decoration: none; color: #007bff; }
        .post {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .post img {
            max-width: 100%;
            border-radius: 6px;
            margin-top: 10px;
        }
        .meta {
            color: gray;
            font-size: 0.9em;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
    <p>You are logged in as <strong><?php echo htmlspecialchars($_SESSION["role"]); ?></strong>.</p>

    <p>
        <a href="pages/add_post.php">+ Add New Post</a> |<!--Add new post page-->
        <a href="pages/logout.php">Logout</a>
    </p>
    <hr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post">
                <h1><?php echo htmlspecialchars($row["title"]); ?></h1>
                <p class="meta">
                    Posted by <?php echo htmlspecialchars($row["username"]); ?> 
                    on <?php echo $row["created_at"]; ?>
                </p>

                <!-- Markdown content placeholder -->
                <div class="markdown-content" data-content="<?php echo htmlspecialchars($row['content'], ENT_QUOTES); ?>"></div>


                <?php if (!empty($row["image"])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($row["image"]); ?>" alt="Post image">
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No posts yet. Be the first to create one!</p>
    <?php endif; ?>

    <!--Include Showdown.js to render Markdown -->
    <script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
<script>
    const converter = new showdown.Converter({ emoji: true });
    document.querySelectorAll(".markdown-content").forEach(div => {
        const raw = div.getAttribute("data-content");
        div.innerHTML = converter.makeHtml(raw);
    });
</script>
</body>
</html>