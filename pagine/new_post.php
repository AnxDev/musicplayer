<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="stylesheet" href="../css/new_post.css">
        <title>home</title>
    </head>
    <body>
        
        <div class="container">
    <form action="../backend/salva_post.php" method="POST" enctype="multipart/form-data" class="album-form">
        <h1>Nuovo post</h1>
        
        <div class="input-group">
            <label>Testo Post</label>
            <input type="text" name="text_content" placeholder="Testo post" required>
        </div>

        <div class="input-group">
        <label>Immagine Post</label>
        <label for="cover-upload" class="cover-preview-wrapper">
            <div id="preview-container">
                <span id="placeholder-text"></span>
                <img id="image-preview" src="#" alt="Preview" style="display:none;">
            </div>
        </label>
        <input type="file" name="image_content" id="cover-upload" accept="image/*" required onchange="previewImage(event)">
        </div>

        <div class="form-actions">
        <a href="home.php" class="btn-secondary">Annulla</a>
        <button type="submit" class="btn-primary">Crea Post (20 token)</button>
        
        </div>
    </form>
</div>
    <script src="scripts/cover_preview.js"></script>
    </body>
</html>