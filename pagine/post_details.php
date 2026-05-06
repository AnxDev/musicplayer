<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
include '../backend/db.php';
$postId = $_GET['id'];
$user_id = $_SESSION['id'];

$query = "SELECT posts.*, users.username, users.id AS user_id_record, 
              (SELECT COUNT(*) FROM comments WHERE id_post = posts.id) AS comment_count, 
              (SELECT COUNT(*) FROM interactions WHERE id_element = posts.id AND element = 'post' AND type = 'like') AS numero_like,
              (SELECT COUNT(*) FROM interactions WHERE id_element = posts.id AND id_user = ? AND element = 'post' AND type = 'like') AS gia_messo_like
              FROM posts
              JOIN users ON posts.id_user = users.id 
              WHERE posts.id = ?
              ORDER BY posts.id DESC";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $postId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) == 0) {
    echo "<p id='error'>Post non trovato</p>";
    exit();
}
$row = mysqli_fetch_assoc($result);

?>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/post.css">
    <title>Post</title>
</head>

<body>
    <?php include '../recycled/header.php'; ?>
    <div class="container">
        <h1>Post</h1>
        <div class="post-card">
            <div class="post-cover">
                <?php echo '<img src="../' . $row['image_content'] . '" alt="Cover">'; ?>
            </div>
            <div class="post-info">
                <h3><?php echo $row['text_content']; ?></h3>
                <p>Pubblicato da: <?php echo $row['username']; ?></p>
                <div class="actions-container">
                    <div class="like-container">
                        <button class="like-btn <?php echo ($row['gia_messo_like'] > 0) ? 'liked' : ''; ?>"
                            onclick="likeToggle(this, <?php echo $postId; ?>)">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </button>
                        <span class="like-count"><?php echo $row['numero_like']; ?></span>
                    </div>
                    <div class="comment-container">
                        <a href="post_details.php?id=<?php echo $row['id']; ?>" class="comment-btn">
                            <svg viewBox="0 0 24 24" class="comment-icon-glow">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                            </svg>
                        </a>
                        <span class="comment-count"><?php echo $row['comment_count'] ?? 0; ?></span>
                    </div>
                </div>
                <p>Commenta:</p>
                <form action="../backend/add_comment.php" method="POST">
                    <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                    <textarea name="text_content" placeholder="Scrivi un commento..." required></textarea>
                    <button type="submit">Invia (5 token)</button>
                </form>
                <div class="comments-container">
                    <?php
                    $query = "SELECT comments.*, users.username, users.pfp,
                              (SELECT COUNT(*) FROM interactions WHERE id_element = comments.id AND element = 'comment' AND type = 'like') AS like_count,
                              (SELECT COUNT(*) FROM interactions WHERE id_element = comments.id AND element = 'comment' AND type = 'like' AND id_user = ?) AS user_liked
                              FROM comments JOIN users ON comments.id_user = users.id WHERE comments.id_post = ? AND comments.id_parent IS NULL ORDER BY comments.creation_date DESC";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "ii", $user_id, $postId);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    while ($comment = mysqli_fetch_assoc($result)) {
                    ?>

                        <div class="comment-card">
                            <img src="../<?php echo $comment['pfp']; ?>" alt="Profile Picture" class="comment-pfp">
                            <p>Data di creazione: <?php echo $comment['creation_date']; ?></p>
                            <p><a href="../pagine/profile.php?username=<?php echo $comment['username']; ?>"><?php echo $comment['username']; ?></a>: <?php echo $comment['text_content']; ?></p>
                            <p><a href="javascript:void(0)" onclick="showReplies(<?php echo $comment['id']; ?>)">Mostra risposte</a></p>

                            <div class="replies-container" id="replies-<?php echo $comment['id']; ?>" style="margin-left: 20px; display: none;">
                                <?php
                                $query = "SELECT comments.*, users.username, users.pfp,
                                          (SELECT COUNT(*) FROM interactions WHERE id_element = comments.id AND element = 'comment' AND type = 'like') AS like_count,
                                          (SELECT COUNT(*) FROM interactions WHERE id_element = comments.id AND element = 'comment' AND type = 'like' AND id_user = ?) AS user_liked
                                          FROM comments JOIN users ON comments.id_user = users.id WHERE comments.id_parent = ? ORDER BY comments.creation_date ASC";
                                $stmt = mysqli_prepare($conn, $query);
                                mysqli_stmt_bind_param($stmt, "ii", $user_id, $comment['id']);
                                mysqli_stmt_execute($stmt);
                                $replies_result = mysqli_stmt_get_result($stmt);
                                while ($reply = mysqli_fetch_assoc($replies_result)) {
                                ?>
                                    <div class="reply-card">
                                        <img src="../<?php echo $reply['pfp']; ?>" alt="Profile Picture" class="reply-pfp">
                                        <p>Data di creazione: <?php echo $reply['creation_date']; ?></p>
                                        <p><a href="../pagine/user_profile.php?id=<?php echo $reply['id_user']; ?>"><?php echo $reply['username']; ?></a>: <?php echo $reply['text_content']; ?></p>
                                        <p>Risposte a questa risposta:</p>
                                        <div class="replies-container" style="margin-left: 20px;">
                                            <?php
                                            $query = "SELECT comments.*, users.username, users.pfp,
                                                      (SELECT COUNT(*) FROM interactions WHERE id_element = comments.id AND element = 'comment' AND type = 'like') AS like_count,
                                                      (SELECT COUNT(*) FROM interactions WHERE id_element = comments.id AND element = 'comment' AND type = 'like' AND id_user = ?) AS user_liked
                                                      FROM comments JOIN users ON comments.id_user = users.id WHERE comments.id_parent = ? ORDER BY comments.creation_date ASC";
                                            $stmt = mysqli_prepare($conn, $query);
                                            mysqli_stmt_bind_param($stmt, "ii", $user_id, $reply['id']);
                                            mysqli_stmt_execute($stmt);
                                            $sub_replies_result = mysqli_stmt_get_result($stmt);
                                            while ($sub_reply = mysqli_fetch_assoc($sub_replies_result)) {
                                            ?>
                                                <div class="reply-card">
                                                    <img src="../<?php echo $sub_reply['pfp']; ?>" alt="Profile Picture" class="reply-pfp">
                                                    <p>Data di creazione: <?php echo $sub_reply['creation_date']; ?></p>
                                                    <p><a href="../pagine/user_profile.php?id=<?php echo $sub_reply['id_user']; ?>"><?php echo $sub_reply['username']; ?></a>: <?php echo $sub_reply['text_content']; ?></p>
                                                    <div class="comment-actions">
                                                        <div class="like-container">
                                                            <button class="like-btn <?php echo ($sub_reply['user_liked'] > 0) ? 'liked' : ''; ?>"
                                                                onclick="likeToggle(this, <?php echo $sub_reply['id']; ?>, 'comment')">
                                                                <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                                            </button>
                                                            <span class="like-count"><?php echo $sub_reply['like_count']; ?></span>
                                                        </div>
                                                        <?php if ($_SESSION['id'] == $sub_reply['id_user']): ?>
                                                            <a href="../backend/delete_comment.php?id=<?php echo $sub_reply['id']; ?>" class="delete-btn">Elimina</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="reply-actions">
                                            <div class="like-container">
                                                <button class="like-btn <?php echo ($reply['user_liked'] > 0) ? 'liked' : ''; ?>"
                                                    onclick="likeToggle(this, <?php echo $reply['id']; ?>, 'comment')">
                                                    <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                                </button>
                                                <span class="like-count"><?php echo $reply['like_count']; ?></span>
                                            </div>
                                            <button class="reply-btn" onclick="showReplyForm(this)" data-comment-id="<?php echo $reply['id']; ?>">Rispondi (5 token)</button>
                                            <?php if ($_SESSION['id'] == $reply['id_user']): ?>
                                                <a href="../backend/delete_comment.php?id=<?php echo $reply['id']; ?>" class="delete-btn">Elimina</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="comment-actions">
                                <div class="like-container">
                                    <button class="like-btn <?php echo ($comment['user_liked'] > 0) ? 'liked' : ''; ?>"
                                        onclick="likeToggle(this, <?php echo $comment['id']; ?>, 'comment')">
                                        <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                    </button>
                                    <span class="like-count"><?php echo $comment['like_count']; ?></span>
                                </div>
                                <button class="reply-btn" onclick="showReplyForm(this)" data-comment-id="<?php echo $comment['id']; ?>">Rispondi (5 token)</button>
                                <?php if ($_SESSION['id'] == $comment['id_user']): ?>
                                    <a href="../backend/delete_comment.php?id=<?php echo $comment['id']; ?>" class="delete-btn">Elimina</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr><br>

                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <script src="scripts/like_handler.js"></script>
    <script>
        function showReplies(commentId) {
            const repliesContainer = document.getElementById(`replies-${commentId}`);
            if (repliesContainer.style.display === 'none') {
                repliesContainer.style.display = 'block';
            } else {
                repliesContainer.style.display = 'none';
            }
        }


        function showReplyForm(button) {
            const commentCard = button.closest('.comment-card');
            let replyForm = commentCard.querySelector('.reply-form');
            if (!replyForm) {
                replyForm = document.createElement('form');
                replyForm.action = '../backend/add_comment.php';
                replyForm.method = 'POST';
                replyForm.classList.add('reply-form');
                replyForm.innerHTML = `
                        <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="id_parent" value="${button.dataset.commentId}">
                        <textarea name="text_content" placeholder="Scrivi una risposta..." required></textarea>
                        <button type="submit">Invia (5 token)</button>
                    `;
                commentCard.appendChild(replyForm);
            } else {
                replyForm.remove();
            }
        }
    </script>
</body>

</html>