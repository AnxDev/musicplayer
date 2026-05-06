<?php
    session_start();
    if (!isset($_SESSION['id'])) {
        header("Location: ../index.php");
        exit();
    }
    include '../backend/db.php';
    $search_query = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/styles.css">
        <title>Ricerca</title>
    </head>
    <body>
        <?php include '../recycled/header.php'; ?>
        <h2>Risultati per: "<?php echo htmlspecialchars($search_query); ?>"</h2>
        
        <p>Profili:</p>
        <?php
            $query = "SELECT id, username FROM users WHERE username LIKE ?";
            $stmt = mysqli_prepare($conn, $query);
            $like_search = '%' . $search_query . '%';
            mysqli_stmt_bind_param($stmt, "s", $like_search);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 0) {
                echo "<p id='error'>Nessun profilo trovato.</p>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<p><strong><a href="profile.php?username=' . htmlspecialchars($row['username']) . '">@' . htmlspecialchars($row['username']) . '</a></strong></p>';
                }
            }
        ?>
        <p>Posts:</p>
        <?php
            $query = "SELECT posts.id AS post_id, posts.text_content, posts.image_content, posts.id_user, users.username, users.id AS id_utente FROM posts JOIN users ON posts.id_user = users.id WHERE posts.text_content LIKE ? OR posts.image_content LIKE ?";
            $stmt = mysqli_prepare($conn, $query);
            $like_search = '%' . $search_query . '%';
            mysqli_stmt_bind_param($stmt, "ss", $like_search, $like_search);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 0) {
                echo "<p id='error'>Nessun album trovato.</p>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<p><strong><a href="album.php?user_id=' . htmlspecialchars($row['id_utente']) . '&album_id=' . htmlspecialchars($row['post_id']) . '">' . htmlspecialchars($row['text_content']) . '</a></strong> di @' . htmlspecialchars($row['username']) . '</p>';
                }
            }
        ?>
    </body>
</html>