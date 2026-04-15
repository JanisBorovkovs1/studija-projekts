<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Pieeja liegta.");
}

// Iegūstam visus pieteikumus ar pieteicēja epastu un sludinājuma atrašanās vietu
$result = $mysqli->query("
    SELECT a.*, u.email as applicant_email, l.location 
    FROM jb_applications a
    JOIN jb_users u ON a.applicant_id = u.id_users
    JOIN jb_listings l ON a.listing_id = l.id_listings
    ORDER BY a.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pieteikumu pārvaldība</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-envelope-paper border p-2 rounded bg-white me-2"></i> Visi pieteikumi</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary">Atpakaļ uz Dashboard</a>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Datums</th>
                            <th>Pieteicējs</th>
                            <th>Sludinājums</th>
                            <th>Ziņa</th>
                            <th>Statuss</th>
                            <th>Lasīts?</th>
                            <th>Darbības</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="<?= $row['is_read'] == 0 ? 'table-primary' : '' ?>">
                            <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                            <td><?= htmlspecialchars($row['applicant_email']) ?></td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <td><small><?= mb_strimwidth(htmlspecialchars($row['message']), 0, 30, "...") ?></small></td>
                            <td>
                                <span class="badge bg-<?= $row['status'] == 'approved' ? 'success' : ($row['status'] == 'rejected' ? 'danger' : 'secondary') ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['is_read'] == 0): ?>
                                    <span class="badge bg-warning text-dark">Jauns</span>
                                    <a href="mark_as_read.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary py-0" title="Atzīmēt kā izlasītu">
                                        <i class="bi bi-check-all"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Izlasīts</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="admin_edit_application.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark"><i class="bi bi-pencil"></i></a>
                                    <a href="admin_delete_application.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Tiešām dzēst?')"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>