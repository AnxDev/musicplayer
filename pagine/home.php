<?php
include '../backend/db.php';
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Home</title>
</head>

<body>
    <h1>Home</h1>
    <p>Benvenuto, <?php echo $_SESSION['username']; ?>!</p>
    <div class="projects-container">
    <div class="projects-grid">
        <?php
            $query = "SELECT album.*,album.id AS album_id, users.id AS user_id, users.username FROM album JOIN users ON album.id_utente = users.id WHERE album.id_utente = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 0) {
                echo "<p id='error'><a href='new_album.php'>Pubblica il tuo primo progetto!</a></p>";
            }
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <b></b>
        <div class="project-card" onclick="window.location.href='album.php?user_id=<?php echo $row['user_id']; ?>&album_id=<?php echo $row['album_id']; ?>'">
            <div class="project-cover">
                <?php echo '<img src="../' . $row['cover'] . '" alt="Cover">'; ?>
                <div class="play-overlay"><span>▶</span></div>
            </div>
            <div class="project-info">
                <h3><?php echo $row['titolo']; ?></h3>
                <p><?php echo $row['username']; ?></p>
            </div>
        </div>
        <?php } ?>
        </div>
        
    </div>
    <a href="new_album.php" class="fab-button" title="Aggiungi nuovo progetto">
            <span>+</span>
        </a>
    <a href="../backend/logout.php">Logout</a>
</body>

</html>