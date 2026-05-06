<?php
include '../backend/db.php';
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
$userId = $_SESSION['id'];
$today = date('Y-m-d');

$checkReward = mysqli_query($conn, "SELECT last_daily_bonus FROM users WHERE id = $userId");
$userData = mysqli_fetch_assoc($checkReward);

if ($userData['last_daily_bonus'] !== $today) {
    $amount = 50; // Regalo automatico
    mysqli_query($conn, "UPDATE users SET token_balance = token_balance + $amount, last_daily_bonus = '$today' WHERE id = $userId");
    
    // Messaggio opzionale da mostrare con un alert
    echo "<script>alert('Bentornato! Hai ricevuto $amount token in regalo per oggi.');</script>";
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/home.css">
    <title>Home</title>
</head>

<body>
    <?php include '../recycled/header.php'; ?>

    <div class="profile-header" onclick="window.location.href='edit_profile.php';">
        <?php
        $query = "SELECT
                pfp,
                bio,
                token_balance,
                (
                SELECT COUNT(i.id) AS totale_like_ricevuti
                FROM interactions i
                LEFT JOIN posts p ON (i.id_element = p.id AND i.element = 'post')
                LEFT JOIN comments c ON (i.id_element = c.id AND i.element = 'comment')
                WHERE (p.id_user = ? OR c.id_user = ?)
                AND i.type = 'like') AS likes
            FROM
                users
            WHERE
                id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iii", $_SESSION['id'], $_SESSION['id'], $_SESSION['id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user_data = mysqli_fetch_assoc($result);
        ?>
        <div class="profile-pic">
            <?php echo '<img src="../' . $user_data['pfp'] . '" alt="Profile Picture">'; ?>
        </div>
        <div class="profile-info">
            <h1>@<?php echo $_SESSION['username']; ?></h1>
            <p><?php echo htmlspecialchars($user_data['bio']); ?></p>
            <p>Token: <?php echo $user_data['token_balance']; ?></p>
            <p>Likes: <?php echo $user_data['likes']; ?></p>
        </div>
    </div>
    <div class="projects-container">
        <div class="projects-grid">
            <?php
            $query = "SELECT posts.*,posts.id AS post_id,users.username FROM posts JOIN users ON posts.id_user = users.id WHERE posts.id_user = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 0) {
                echo "<p id='error'><a href='new_post.php'>Pubblica il tuo primo post!</a></p>";
            }
            while ($row = mysqli_fetch_assoc($result)) {
            ?>

                <div class="project-card">
                    <div class="project-cover" onclick="window.location.href='post_details.php?id=<?php echo $row['id']; ?>';">
                        <?php echo '<img src="../' . $row['image_content'] . '" alt="image_content">'; ?>
                    </div>
                    <div class="project-info">
                        <h3><?php echo $row['text_content']; ?></h3>
                        <p><?php echo $row['username']; ?></p>
                        <p><a href="../backend/delete_post.php?id=<?php echo $row['post_id']; ?>" class="delete-btn" onclick="return confirm('Sei sicuro di voler eliminare questo post?');">Elimina</a></p>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
    <a href="new_post.php" class="fab-button" title="Aggiungi nuovo post">
        <span>+</span>
    </a>

</body>

</html>