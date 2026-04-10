<?php
session_start();
require 'db.php';
$owner_id = $_SESSION['id_users'];

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

# Atzīmēt visus pieteikumus kā izlasītus
$stmt = $mysqli->prepare("
UPDATE jb_applications 
SET is_read = 1 
WHERE owner_id = ?
");
$stmt->bind_param("i", $owner_id);
$stmt->execute();

# Iegūt visus paziņojumus
$stmt = $mysqli->prepare("
    SELECT a.*, u.email
    FROM jb_applications a
    JOIN jb_users u ON a.applicant_id = u.id_users
    WHERE a.owner_id = ?
    ORDER BY a.created_at DESC
");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paziņojumi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>

<body class="p-4 bg-light">

<div class="container fade-in" style="max-width: 800px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Jūsu paziņojumi</h2>
        <a href="next.php" class="btn btn-outline-secondary">
            ⬅ Atpakaļ
        </a>
    </div>

    <div class="list-group shadow-sm">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list-group-item list-group-item-action p-4">
                    <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold text-primary">Jauns pieteikums!</h6>
                        <small class="text-muted text-nowrap ms-3">
                            <?= date('d.m.Y H:i', strtotime($row['created_at'])) ?>
                        </small>
                    </div>
                    
                    <p class="mb-2">
                        Lietotājs <strong><?= htmlspecialchars($row['email']) ?></strong> vēlas īrēt jūsu telpu.
                    </p>

                    <?php if (!empty($row['message'])): ?>
                        <div class="p-3 bg-light border rounded text-dark small">
                            <strong>Atstātie kontakti:</strong><br>
                            <?= nl2br(htmlspecialchars($row['message'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="p-5 text-center text-muted border rounded bg-white">
                <h4 class="mb-3">📭 Paziņojumu nav</h4>
                <p class="mb-0">Šobrīd neviens vēl nav pieteicies uz jūsu sludinājumiem.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>