<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/leaderboard.css">
    <title>Classifica</title>
</head>
<body>
    <?php include '../recycled/header.php'; ?>
    
    <div class="leaderboard-container">
        <h1>Classifica Utenti</h1>
        <div class="table-wrapper">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Pos</th>
                        <th>Username</th>
                        <th>Token</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include '../backend/db.php';
                        // Query per i token
                        $query = "SELECT username, token_balance, pfp FROM users ORDER BY token_balance DESC LIMIT 50";
                        $result = mysqli_query($conn, $query);
                        $position = 1;
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Assegna una classe speciale ai primi 3
                            $rankClass = '';
                            if ($position == 1) $rankClass = 'rank-gold';
                            elseif ($position == 2) $rankClass = 'rank-silver';
                            elseif ($position == 3) $rankClass = 'rank-bronze';

                            echo "<tr class='$rankClass'>";
                            echo "<td class='pos-cell'><span>" . $position . "</span></td>";
                            // use $row["pfp"] for profile picture if available
                            echo "<td class='user-cell'><img src='../" . htmlspecialchars($row['pfp']) . "' alt='Avatar' class='avatar'> <a href='../pagine/profile.php?username=" . htmlspecialchars($row['username']) . "'>@" . htmlspecialchars($row['username']) . "</a></td>";
                            
                            echo "<td class='token-cell'>" . number_format($row['token_balance'], 0, ',', '.') . " <span class='token-icon'>$</span></td>";
                            echo "</tr>";
                            $position++;
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>