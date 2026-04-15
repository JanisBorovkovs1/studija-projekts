<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') die("Pieeja liegta.");

$result = $mysqli->query("
    SELECT a.*, u.email as applicant_email, l.location 
    FROM jb_applications a
    JOIN jb_users u ON a.applicant_id = u.id_users
    JOIN jb_listings l ON a.listing_id = l.id_listings
    ORDER BY a.is_read ASC, a.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pieteikumi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Visi pieteikumi</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary">Atpakaļ</a>
        </div>

        <div class="card shadow-sm">
            <table class="table align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Datums</th>
                        <th>Pieteicējs</th>
                        <th>Vieta</th>
                        <th>Statuss</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="<?= $row['is_read'] == 0 ? 'fw-bold table-primary' : 'text-muted' ?>">
                        <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= htmlspecialchars($row['applicant_email']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td>
                            <?php if ($row['is_read'] == 0): ?>
                                <span class="badge bg-warning text-dark">Gaida</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border">Izlasīts</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['is_read'] == 0): ?>
                                <a href="mark_as_read.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Atzīmēt kā izlasītu</a>
                            <?php endif; ?>
                            <a href="admin_delete_application.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Dzēst?')">Dzēst</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>