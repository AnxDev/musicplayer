<?php
include '../backend/db.php';
session_start();

// 1. Controllo Sessione
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

// 2. Controllo Parametri URL
if (isset($_GET['album_id']) && isset($_GET['user_id'])) {
    $album_id = (int)$_GET['album_id'];
    $user_id = (int)$_GET['user_id'];

    // Recupero dati Album e nome Artista
    $query = "SELECT album.*, users.username FROM album JOIN users ON album.id_utente = users.id WHERE album.id = ? AND users.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $album_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $album_data = mysqli_fetch_assoc($result);
} else {
    die("Parametri mancanti.");
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/album.css">
    <link rel="stylesheet" href="../css/player.css">

    <title><?php echo $album_data ? htmlspecialchars($album_data['titolo']) : 'Album'; ?></title>
</head>
<body class="album-page">

    <?php if ($album_data): ?>
        <div class="album-header">
            <div class="album-cover-large">
                <img src="../<?php echo htmlspecialchars($album_data['cover']); ?>" alt="Cover">
            </div>
            
            <div class="album-details">
                <span class="type-label">ALBUM</span>
                <h1><?php echo htmlspecialchars($album_data['titolo']); ?></h1>
                <p>Pubblicato da: <strong><?php echo htmlspecialchars($album_data['username']); ?></strong></p>
            </div>
        </div>

        <div class="tracklist">
            <div class="tracklist-header">
                <span>#</span>
                <span>TITOLO</span>
                <span class="text-right">DURATA</span>
            </div>
            
            <?php
            // Recupero brani
            $query_brani = "SELECT * FROM brani WHERE id_album = ?";
            $stmt_brani = mysqli_prepare($conn, $query_brani);
            mysqli_stmt_bind_param($stmt_brani, "i", $album_id);
            mysqli_stmt_execute($stmt_brani);
            $res_brani = mysqli_stmt_get_result($stmt_brani);

            $i = 1;
            while ($brano = mysqli_fetch_assoc($res_brani)): ?>
                <div class="track-row" style="cursor: pointer;" 
                onclick="playSong('../<?php echo $brano['path']; ?>', '<?php echo addslashes($brano['titolo']); ?>')">
                    <span class="track-number"><?php echo $i++; ?></span>
                    <div class="track-info">
                        <span class="track-title"><?php echo htmlspecialchars($brano['titolo']); ?></span>
                    </div>
                    <span class="track-duration text-right"><?php echo htmlspecialchars($brano['durata']); ?></span>
                </div>
            <?php endwhile; ?>

            <?php 
            // In album_page.php deve esserci il controllo: $is_owner = ($_SESSION['id'] == $album_data['id_utente']);
            require '../backend/album_page.php'; 
            if ($is_owner): ?>
                <div class="owner-actions">
                    <a href="../backend/aggiungi_brano.php?id_album=<?php echo $album_id; ?>" class="btn-add-track">
                        <span class="plus-icon">+</span> Aggiungi brano
                    </a>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <p id="error">Album non trovato.</p>
    <?php endif; ?>
    <div id="global-player" class="player-bar">
        <div class="player-content">
            <div class="song-info">
                <span id="player-title">Nessun brano</span>
                <span id="player-artist"><?php echo htmlspecialchars($album_data['username']); ?></span>
            </div>
            
            <div class="player-controls">
    <audio id="main-audio" src=""></audio>
    <button id="play-pause" class="btn-play">▶</button>
    
    <div class="progress-container">
        <div class="progress-bg">
            <div id="progress-bar" class="progress-fill"></div>
        </div>
    </div>
</div>
            
            <div class="player-meta">
                <span id="current-time">00:00</span>
            </div>
        </div>
    </div>
    <script src="scripts/player.js"></script>
</body>
</html>