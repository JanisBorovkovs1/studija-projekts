<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') die("Pieeja liegta.");

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
    <title>Pieteikumu kontrole</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Visi sistēmas pieteikumi</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary">Atpakaļ</a>
        </div>

        <div class="card shadow-sm border-0">
            <table class="table align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Datums</th>
                        <th>Pieteicējs</th>
                        <th>Vieta</th>
                        <th>Statuss pie lietotāja</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= htmlspecialchars($row['applicant_email']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td>
                            <?php if ($row['is_read'] == 0): ?>
                                <span class="badge bg-primary">Nav lasīts</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border">Izlasīts</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <?php if ($row['is_read'] == 0): ?>
                                    <a href="mark_as_read.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-success">Atzīmēt kā lasītu</a>
                                <?php endif; ?>
                                <a href="admin_delete_application.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tiešām dzēst?')">Dzēst</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>