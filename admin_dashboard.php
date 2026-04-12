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

# Kārtošana
$sort = $_GET['sort'] ?? 'newest';
$order_by = "ORDER BY created_at DESC"; 

if ($sort === 'price_asc') $order_by = "ORDER BY price ASC";
elseif ($sort === 'price_desc') $order_by = "ORDER BY price DESC";
elseif ($sort === 'location_asc') $order_by = "ORDER BY location ASC";
elseif ($sort === 'location_desc') $order_by = "ORDER BY location DESC";

$result = $mysqli->query("SELECT * FROM jb_listings $order_by");
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">

<h2>Admin Dashboard</h2>

<div class="d-flex justify-content-between mb-3 align-items-end flex-wrap gap-3">
    <div>
        <a href="next.php" class="btn btn-secondary">Atpakaļ</a>
        <a href="admin_add_listing.php" class="btn btn-success">Pievienot sludinājumu</a>

        <a href="archive.php" class="btn btn-info text-white">📜 Sistēmas arhīvs</a>
    </div>

    <form method="GET" class="d-flex align-items-center gap-2">
        <label class="fw-bold text-nowrap">Sort by:</label>
        <select name="sort" class="form-select" onchange="this.form.submit()">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Jaunākie</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Lētākie vispirms</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Dārgākie vispirms</option>
            <option value="location_asc" <?= $sort === 'location_asc' ? 'selected' : '' ?>>Atrašanās vieta (A-Z)</option>
            <option value="location_desc" <?= $sort === 'location_desc' ? 'selected' : '' ?>>Atrašanās vieta (Z-A)</option>
            </select>
        </form>
        </select>
    </form>
</div>

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