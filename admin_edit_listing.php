<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM jb_listings WHERE id_listings=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$location = $_POST['location'];
$contact = $_POST['contact'];
$price = $_POST['price'];
$description = $_POST['description'];

$stmt = $mysqli->prepare("
UPDATE jb_listings
SET location=?, contact=?, price=?, description=?
WHERE id_listings=?
");

$stmt->bind_param("ssdsi",$location,$contact,$price, $description, $id);

if ($stmt->execute()) {
        logActivity($mysqli, $_SESSION['id_users'], 'Rediģēts sludinājums', "Adminis izmainīja sludinājumu (ID: $id). Jaunā vieta: $location, jaunā cena: $price, jaunais kontakts: $contact, jaunais apraksts: $description");
        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Rediģēt sludinājumu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="card-title fw-bold mb-4">Rediģēt sludinājumu #<?= $id ?></h3>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Atrašanās vieta</label>
                        <input name="location" class="form-control" value="<?= htmlspecialchars($row['location']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kontakti</label>
                        <input name="contact" class="form-control" value="<?= htmlspecialchars($row['contact']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cena (EUR)</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="<?= $row['price'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Apraksts</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($row['description']) ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success w-100">Saglabāt izmaiņas</button>
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary w-100">Atcelt</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>