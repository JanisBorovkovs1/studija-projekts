<?php
date_default_timezone_set('Europe/Riga');
session_start();

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}
$conn = new mysqli("localhost", "u547027111_mvg", "MVGskola1", "u547027111_mvg");
$conn->query("SET time_zone = '+02:00'");
# Iegūst lietotāja ID
$owner_id = $_SESSION['id_users'];
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

# Iegūt visus sludinājumus
$result = $conn->query("SELECT * FROM jb_listings ORDER BY created_at DESC");
?>

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
<a href="logout.php" class="btn btn-danger">Logout</a>
<a href="notifications.php" class="btn btn-warning position-relative">
    🔔 Paziņojumi (<?= $notification_count ?>)
    <?php if ($notification_count > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $notification_count ?>
        </span>
    <?php endif; ?>

</a>
<?php if ($_SESSION['role'] === 'admin'): ?>
<a href="admin_dashboard.php" class="btn btn-dark">Admin Panel</a>
<?php endif; ?>
<div class="container fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Studiju īres piedāvājumi</h2>
        <a href="izveidot.php" class="btn btn-success">
            + Izveidot savu
        </a>
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
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php $conn->close(); ?>

</body>
</html>