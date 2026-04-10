<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$result = $mysqli->query("SELECT * FROM jb_listings ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">

<h2>Admin Dashboard</h2>

<a href="next.php" class="btn btn-secondary mb-3">Atpakaļ</a>
<a href="admin_add_listing.php" class="btn btn-success mb-3">Pievienot sludinājumu</a>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">


    <tr>
    <th>ID</th>
    <th>Location</th>
    <th>Contact</th>
    <th>Price</th>
    <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>

    <tr>
    <td><?= $row['id_listings'] ?></td>
    <td><?= htmlspecialchars($row['location']) ?></td>
    <td><?= htmlspecialchars($row['contact']) ?></td>
    <td><?= $row['price'] ?></td>

    <td>
    <a class="btn btn-warning btn-sm"
    href="admin_edit_listing.php?id=<?= $row['id_listings'] ?>">
    Edit
    </a>

    <a class="btn btn-danger btn-sm"
    href="admin_delete_listing.php?id=<?= $row['id_listings'] ?>">
    Delete
    </a>
    </td>

    </tr>

<?php endwhile; ?>

    </table>
</div>
</body>
</html>