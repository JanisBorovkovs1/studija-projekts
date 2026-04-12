<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

// Pārbaudām, vai ir admins (drošībai)
if ($_SESSION['role'] !== 'admin') {
    die("Piekļuve liegta. Šī lapa ir tikai administratoriem.");
}

// Iegūstam arhīva datus kopā ar lietotāja e-pastu
$result = $mysqli->query("
    SELECT a.*, u.email 
    FROM jb_activity_log a
    LEFT JOIN jb_users u ON a.user_id = u.id_users
    ORDER BY a.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Sistēmas Arhīvs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Sistēmas Darbību Arhīvs</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary">⬅ Atpakaļ uz Admin Panel</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Laiks</th>
                        <th>Lietotājs</th>
                        <th>Darbības tips</th>
                        <th>Detaļas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-nowrap text-muted">
                                    <?= date('d.m.Y H:i:s', strtotime($row['created_at'])) ?>
                                </td>
                                <td><strong><?= htmlspecialchars($row['email'] ?? 'Nezināms') ?></strong></td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($row['action_type']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($row['details']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center p-4 text-muted">Arhīvs šobrīd ir tukšs.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>