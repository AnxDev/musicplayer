<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="stylesheet" href="../css/newalbum.css">
        <title>home</title>
    </head>
    <body>
        
        <div class="container">
    <form action="../backend/salva_album.php" method="POST" enctype="multipart/form-data" class="album-form">
        <h1>Nuovo Album</h1>
        
        <div class="input-group">
            <label>Titolo Album</label>
            <input type="text" name="titolo" placeholder="Titolo album" required>
        </div>

        <div class="input-group">
            <label>Data di Pubblicazione</label>
            <input type="date" name="data_rilascio" required>
        </div>

        <div class="input-group">
        <label>Copertina Album</label>
        <label for="cover-upload" class="cover-preview-wrapper">
            <div id="preview-container">
                <span id="placeholder-text"></span>
                <img id="image-preview" src="#" alt="Preview" style="display:none;">
            </div>
        </label>
        <input type="file" name="cover" id="cover-upload" accept="image/*" required onchange="previewImage(event)">
        </div>

        <div class="form-actions">
        <a href="home.php" class="btn-secondary">Annulla</a>
        <button type="submit" class="btn-primary">Crea Album</button>
        
        </div>
    </form>
</div>
    <script src="scripts/cover_preview.js"></script>
    </body>
</html>