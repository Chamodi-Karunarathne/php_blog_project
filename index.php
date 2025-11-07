<?php
session_start();
include 'config.php';
//include 'components/top_liked.php';     // Include top liked posts

// Fetch all posts for public view
$perPage = 6;
$page    = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset  = ($page - 1) * $perPage;

$sql = "SELECT posts.*, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY posts.created_at DESC
    LIMIT $perPage OFFSET $offset";
$result = $conn->query($sql);

$countRes = $conn->query("SELECT COUNT(*) AS total FROM posts");
$totalPosts = $countRes ? (int) $countRes->fetch_assoc()['total'] : 0;
$hasMore = ($offset + $perPage) < $totalPosts;

$isLoggedIn = isset($_SESSION["user_id"]);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Home | Blog</title>
    <link rel="stylesheet" href="/php_blog_project/assets/style.css">
    <link rel="stylesheet" href="/php_blog_project/assets/nav.css">
    <link rel="stylesheet" href="/php_blog_project/assets/footer.css">
</head>
<body>
<?php include 'components/navbar.php'; ?>
<main class="home-layout">

    <div class="main-content">
        <?php include 'components/top_liked.php'; ?>
        <div class="posts-container" data-current-page="<?php echo $page; ?>" data-per-page="<?php echo $perPage; ?>">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $hasImage = !empty($row['image']);
                    $postLink = $isLoggedIn
                        ? 'pages/view_post.php?id=' . $row['id']
                        : 'pages/login.php';
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
                        <div class="markdown-content" 
                            data-content="<?php echo htmlspecialchars($row['content'], ENT_QUOTES); ?>">
                        </div>
                    </div>

                    <footer class="post-card__footer">
                        <?php $post_id = $row['id']; include 'components/like_button.php'; ?>
                        <a href="<?php echo $postLink; ?>" class="post-card__link">
                            Read Article →
                        </a>
                    </footer>
                </article>
            <?php endwhile; ?>
                </div>
                <?php if ($hasMore): ?>
                    <div class="home-load-more">
                        <button class="btn-outline" id="load-more-posts" data-next-page="<?php echo $page + 1; ?>">
                            Load More Posts
                        </button>
                    </div>
                <?php endif; ?>
    </div> <!-- end of main-content -->

</main>

<!-- Markdown Rendering -->
<script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
<script>
  const converter = new showdown.Converter({ emoji: true });
    function hydrateMarkdown(scope = document) {
        scope.querySelectorAll('.markdown-content').forEach(div => {
            if (div.dataset.hydrated === 'true') return;
            const raw = div.getAttribute('data-content');
            let html = converter.makeHtml(raw);
            const maxLength = 180;
            if (html.length > maxLength) {
                html = html.substring(0, maxLength).trimEnd() + '...';
            }
            div.innerHTML = html;
            div.dataset.hydrated = 'true';
        });
    }

    hydrateMarkdown();

    const loadMoreBtn = document.getElementById('load-more-posts');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', async () => {
            const container = document.querySelector('.posts-container');
            if (!container) return;

            const perPage = parseInt(container.dataset.perPage, 10) || 6;
            const nextPage = parseInt(loadMoreBtn.dataset.nextPage, 10) || 2;

            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'Loading...';

            try {
                const response = await fetch(`/php_blog_project/pages/load_posts.php?page=${nextPage}&per_page=${perPage}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.text();

            const temp = document.createElement('div');
            temp.innerHTML = data.trim();

            const newCards = temp.querySelectorAll('.post');
            newCards.forEach(card => container.appendChild(card));
            hydrateMarkdown(container);

                const hasMore = temp.querySelector('[data-has-more="false"]');
                if (hasMore) {
                    loadMoreBtn.remove();
                } else {
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.textContent = 'Load More Posts';
                    loadMoreBtn.dataset.nextPage = nextPage + 1;
                }
            } catch (error) {
                console.error('Unable to load more posts', error);
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load More Posts';
            }
        });
    }
</script>

<?php include 'components/like_script.php'; ?>
<?php include 'components/footer.php'; ?>

</body>
</html>
