<script>
document.querySelectorAll('.like-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const section = this.closest('.like-section');
    const postId = section.dataset.postId;
    const heart = section.querySelector('.heart');
    const countSpan = section.querySelector('.like-count');

    fetch(window.location.pathname.includes('/pages/') ? 'like_posts.php' : 'pages/like_posts.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'post_id=' + postId
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        heart.classList.toggle('liked', data.liked);
        countSpan.textContent = data.total_likes;
      } else {
        alert(data.message);
      }
    })
    .catch(err => console.error('Error:', err));
  });
});

</script>
