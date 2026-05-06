
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/feed.css">
    <title>Home</title>
</head>
<body>
    <?php include_once '../recycled/header.php'; ?>

    <div class="feed-container">
        <?php
        include_once '../backend/feed_logic.php';
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <section class="feed-item" >
            <div class="feed-cover" onclick="window.location.href='post_details.php?id=<?php echo $row['id']; ?>'">
                <?php echo '<img src="../' . $row['image_content'] . '" alt="image_content">'; ?>
            </div>
            <div class="feed-footer">
            <div class="feed-info">
                <h3><?php echo htmlspecialchars($row['text_content']); ?></h3>
                <p>
                    <a href="profile.php?username=<?php echo htmlspecialchars($row['username']); ?>">
                        @<?php echo htmlspecialchars($row['username']); ?>
                    </a>
                </p>
            </div>
            <div class="actions-container">
            <div class="like-container">
                <button class="like-btn <?php echo ($row['gia_messo_like'] > 0) ? 'liked' : ''; ?>" 
                        onclick="likeToggle(this, <?php echo $row['id']; ?>)">
                    <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </button>
                <span class="like-count"><?php echo $row['numero_like']; ?></span>
            </div>
                <div class="comment-container">
                            <a href="post_details.php?id=<?php echo $row['id']; ?>" class="comment-btn">
                                <svg viewBox="0 0 24 24" class="comment-icon-glow">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
    </svg>
                            </a>
                            <span class="comment-count"><?php echo $row['comment_count'] ?? 0; ?></span>
                </div>
            </div>
        </div>
        </section>
        <?php } ?>
    </div>
    <script src="scripts/like_handler.js"></script>
</body>
</html>
