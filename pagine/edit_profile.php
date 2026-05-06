<!DOCTYPE html>
<?php
session_start();
?>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="stylesheet" href="../css/edit_profile.css">
        <title>Modifica Profilo</title>
    </head>
    <body>
        <?php
        include '../recycled/header.php';
        include '../backend/db.php';
        ?>
        <div class="container">
            <form action="../backend/update_profile.php" method="POST" enctype="multipart/form-data" class="album-form">
                <h1>Modifica Profilo</h1>
                
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Username" value="<?php echo $_SESSION['username']; ?>" required>
                </div>
                <div class="input-group">
                    <label>Bio</label>
                    <textarea name="bio" placeholder="Bio"><?php
                        $query = "SELECT bio FROM users WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $user_data = mysqli_fetch_assoc($result);
                        echo htmlspecialchars($user_data['bio']);
                    ?></textarea>
                </div>
                <div class="input-group">
                    <label>Immagine del profilo</label>
                    <input type="file" name="pfp" accept="image/*">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Salva</button>
                    <a href="home.php" class="btn-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </body>
</html>