<?php
include '../backend/db.php';
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
$user_profile = $_GET['username'];
$query = "SELECT id FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $user_profile);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $user_profile = $row['id'];
} else {
    echo "<p id='error'>Utente non trovato</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/profile.css">
    <title>Home</title>
</head>

<body>
    <?php include '../recycled/header.php'; ?>

    <?php
    if ($user_profile == $_SESSION['id']) {
        echo "<div class='profile-header' onclick='window.location.href=\"edit_profile.php\";'>";
    } else {
        echo "<div class='profile-header'>";
    }
    ?>

    <?php
    $query = "SELECT
                pfp,
                bio,
                token_balance, username,
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
    mysqli_stmt_bind_param($stmt, "iii", $user_profile, $user_profile, $user_profile);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($result);
    ?>
    <div class="profile-pic">
        <?php echo '<img src="../' . $user_data['pfp'] . '" alt="Profile Picture">'; ?>
    </div>
    <div class="profile-info">
        <h1>@<?php echo $user_data['username']; ?></h1>
        <p><?php echo htmlspecialchars($user_data['bio']); ?></p>
        <p>Token: <?php echo $user_data['token_balance']; ?></p>
        <p>Likes: <?php echo $user_data['likes']; ?></p>
    </div>
    </div>
    <div class="projects-container">
        <div class="projects-grid">
            <?php
            $query = "SELECT posts.*, posts.id AS post_id, users.id AS user_id, users.username FROM posts JOIN users ON posts.id_user = users.id WHERE posts.id_user = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_profile);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 0) {
                echo "<p id='error'>Nessun post trovato per questo utente.</p>";
            }
            while ($row = mysqli_fetch_assoc($result)) {
            ?>

                <div class="project-card">
                    <div class="project-cover" onclick="window.location.href='post_details.php?id=<?php echo $row['post_id']; ?>';">
                        <?php echo '<img src="../' . $row['image_content'] . '" alt="Cover">'; ?>
                    </div>
                    <div class="project-info">
                        <h3><?php echo $row['text_content']; ?></h3>
                        <p>@<?php echo $row['username']; ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

</body>

</html>