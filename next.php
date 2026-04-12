<?php
session_start();

$owner_id = $_SESSION['id_users'];

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}
# Savienot ar datubāzi
$conn = new mysqli("localhost", "u547027111_mvg", "MVGskola1", "u547027111_mvg");
# Iegūst lietotāja ID
$owner_id = $_SESSION['id_users'];

if (isset($_GET['get_count'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM jb_applications WHERE owner_id = ? AND is_read IS NULL");
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    echo $res['total'];
    exit();
}

# Skaitīt neizlasītos pieteikumus
$count_stmt = $conn->prepare("
SELECT COUNT(*) as total 
FROM jb_applications 
WHERE owner_id = ? AND is_read IS NULL
");

$count_stmt->bind_param("i", $owner_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();

$notification_count = $count_row['total'];

# Kārtošana
$sort = $_GET['sort'] ?? 'newest';
$order_by = "ORDER BY created_at DESC"; // Noklusētais (jaunākie)

if ($sort === 'price_asc') {
    $order_by = "ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
    $order_by = "ORDER BY price DESC";
} elseif ($sort === 'location_asc') {
    $order_by = "ORDER BY location ASC";
} elseif ($sort === 'location_desc') {
    $order_by = "ORDER BY location DESC";
}

$result = $conn->query("SELECT * FROM jb_listings $order_by");

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Īre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>

<body class="p-4 bg-light">
<div class="container mb-4 fade-in">
    <div class="d-flex gap-2 justify-content-end flex-wrap">
        <a href="notifications.php" class="btn btn-outline-primary position-relative">
            🔔 Paziņojumi
            <span id="notifBadge" 
                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                  style="display: <?= ($notification_count > 0) ? 'inline-block' : 'none'; ?> !important; visibility: visible !important; opacity: 1 !important;">
                <span id="notifCount"><?= $notification_count ?></span>
            </span>
        </a>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin_dashboard.php" class="btn btn-dark">Admin Panel</a>
        <?php endif; ?>
        <a href="my_applications.php" class="btn btn-outline-info">
            📄 Mani pieteikumi
        </a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="fw-bold mb-0">Studiju īres piedāvājumi</h2>
        
        <div class="d-flex gap-3 align-items-center">
            <form method="GET" class="m-0">
                <select name="sort" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Jaunākie vispirms</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Lētākie vispirms</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Dārgākie vispirms</option>
                    <option value="location_asc" <?= $sort === 'location_asc' ? 'selected' : '' ?>>Vieta (A-Z)</option>
                    <option value="location_desc" <?= $sort === 'location_desc' ? 'selected' : '' ?>>Vieta (Z-A)</option>
                </select>
            </form>
            
            <a href="izveidot.php" class="btn btn-success">+ Izveidot savu</a>
        </div>
    </div>

    <div class="row g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    
                    <!-- IMAGE -->
                    <img src="uploads/studio.png" 
                         class="card-img-top"
                         style="height:200px; object-fit:cover;"
                         alt="Studio image">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <?= htmlspecialchars($row['location']) ?>
                        </h5>

                        <p class="card-text text-muted mb-2">
                            📞 <?= htmlspecialchars($row['contact']) ?>
                        </p>

                        <p class="card-text">
                            <?= htmlspecialchars($row['description']) ?>
                        </p>

                        <p class="fw-bold fs-5 mb-3">
                            <?= htmlspecialchars($row['price']) ?> EUR/h
                        </p>

                        <a href="pieteikties.php?listing_id=<?= $row['id_listings'] ?>" 
                           class="btn btn-primary mt-auto">
                            Pieteikties
                        </a>
                        <?php if ($row['owner_id'] == $_SESSION['id_users']): ?>
                            <form action="delete_listing.php" method="post" onsubmit="return confirm('Vai tiešām dzēst?');">
                                <input type="hidden" name="id" value="<?= $row['id_listings'] ?>">
                                <button class="btn btn-danger btn-sm mt-2">Dzēst</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="main.js"></script>
<?php $conn->close(); ?>

</body>
</html>