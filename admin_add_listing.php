<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$location = $_POST['location'];
$contact = $_POST['contact'];
$price = $_POST['price'];
$description = $_POST['description'];
$owner = $_SESSION['id_users'];

$stmt = $mysqli->prepare("
INSERT INTO jb_listings (location, contact, price, description, owner_id)
VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("ssdsi", $location, $contact, $price, $description, $owner);

if ($stmt->execute()) {
    logActivity($mysqli, $_SESSION['id_users'], 'Pievienots sludinājums', "Adminis pievienoja jaunu vietu: " . $_POST['location']);
    header("Location: admin_dashboard.php");
} 
else {
    echo "Error: " . $stmt->error;
}
header("Location: admin_dashboard.php");
exit();
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pievienot sludinājumu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="card-title fw-bold mb-4">Pievienot jaunu sludinājumu</h3>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Atrašanās vieta</label>
                        <input name="location" class="form-control" placeholder="Piemēram, Rīga, Centrs" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kontakti</label>
                        <input name="contact" class="form-control" placeholder="E-pasts vai tel. nr." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cena (EUR)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Apraksts</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Telpas apraksts..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Pievienot</button>
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary w-100">Atpakaļ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>