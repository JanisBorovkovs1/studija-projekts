<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

$applicant_id = $_SESSION['id_users'];

# Iegūt lietotāja pieteikumus kopā ar sludinājuma informāciju
$stmt = $mysqli->prepare("
    SELECT a.id as app_id, a.message, a.created_at, a.is_read, l.location, l.price 
    FROM jb_applications a
    JOIN jb_listings l ON a.listing_id = l.id_listings
    WHERE a.applicant_id = ?
    ORDER BY a.created_at DESC
");
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mani Pieteikumi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>
<body class="p-4 bg-light">

<div class="container fade-in" style="max-width: 800px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Mani pieteikumi</h2>
        <a href="next.php" class="btn btn-outline-secondary">⬅ Atpakaļ</a>
    </div>

    <div class="list-group shadow-sm">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list-group-item p-4">
                    <div class="d-flex w-100 justify-content-between align-items-start mb-2 flex-wrap">
                        <h5 class="mb-1 fw-bold text-dark">
                            Telpa: <?= htmlspecialchars($row['location']) ?> 
                            <span class="badge bg-success ms-2"><?= htmlspecialchars($row['price']) ?> EUR/h</span>
                        </h5>
                        
                        <?php if ($row['is_read'] == 1): ?>
                            <span class="badge bg-secondary">👀 Izlasīts</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">⏳ Gaida apskati</span>
                        <?php endif; ?>
                    </div>
                    
                    <small class="text-muted d-block mb-3">
                        Pieteikuma laiks: <?= date('d.m.Y H:i', strtotime($row['created_at'])) ?>
                    </small>

                    <div class="p-3 bg-light border rounded text-dark small mb-3">
                        <strong>Mans ziņojums/kontakti:</strong><br>
                        <?= nl2br(htmlspecialchars($row['message'])) ?>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="edit_application.php?id=<?= $row['app_id'] ?>" class="btn btn-sm btn-primary">
                            Labot ziņojumu
                        </a>
                        <form action="delete_application.php" method="POST" onsubmit="return confirm('Vai tiešām vēlies atcelt šo pieteikumu?');">
                            <input type="hidden" name="app_id" value="<?= $row['app_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Atcelt pieteikumu</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="p-5 text-center text-muted border rounded bg-white">
                <h4 class="mb-3">📭 Nav pieteikumu</h4>
                <p class="mb-0">Jūs vēl neesat pieteicies nevienam sludinājumam.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>