<?php
include '../backend/db.php';
session_start();

// Controllo se l'utente è loggato e se l'album è specificato
if (!isset($_SESSION['id']) || !isset($_GET['id_album'])) {
    header("Location: ../index.php");
    exit();
}

$id_album = (int)$_GET['id_album'];

// Verifica che l'utente sia effettivamente il proprietario dell'album
$query_check = "SELECT id FROM album WHERE id = ? AND id_utente = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "ii", $id_album, $_SESSION['id']);
mysqli_stmt_execute($stmt_check);
$res_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($res_check) == 0) {
    die("Accesso negato.");
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/album.css"> <title>Aggiungi Brano</title>
</head>
<body class="album-page">

    <div class="tracklist" style="max-width: 500px;">
        <h2 style="font-weight: 300; margin-bottom: 40px; letter-spacing: -1px;">Aggiungi traccia</h2>

        <form action="../backend/salva_brano.php?id_album=<?php echo $id_album; ?>" enctype="multipart/form-data"   method="POST">
            <input type="hidden" name="id_album" value="<?php echo $id_album; ?>">

            <div class="minimal-input-group">
                <label>Titolo</label>
                <input type="text" name="titolo" placeholder="Nome del brano" required>
            </div>

            <div class="minimal-input-group">
                <label>File Audio (MP3)</label>
                <div class="file-wrapper">
                    <input type="file" name="brano" id="audio" accept="audio/mpeg" required onchange="showName(this)">
                    <label for="audio" id="file-label">Seleziona file</label>
                </div>
            </div>

            <div class="minimal-input-group">
                <label>Durata (mm:ss)</label>
                <input type="text" name="durata" placeholder="03:45" required>
            </div>

            <div class="owner-actions" style="border:none; display: flex; gap: 20px;">
                <button type="submit" class="btn-add-track" style="background:none; border:none; cursor:pointer;">
                    <span>+</span> Conferma
                </button>
                <a href="album.php?album_id=<?php echo $id_album; ?>&user_id=<?php echo $_SESSION['id']; ?>" class="btn-add-track" style="opacity:0.4;">
                    Annulla
                </a>
            </div>
        </form>
    </div>

    <script>
    function showName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('file-label').textContent = input.files[0].name;
            document.getElementById('file-label').style.color = "#fff";
        }
    }
    </script>
</body>
</html>