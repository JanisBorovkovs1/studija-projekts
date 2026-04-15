<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') die("Access denied.");

$id = $_GET['id'];
$stmt = $mysqli->prepare("SELECT * FROM jb_applications WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $msg = $_POST['message'];
    $status = $_POST['status'];
    
    $update = $mysqli->prepare("UPDATE jb_applications SET message = ?, status = ? WHERE id = ?");
    $update->bind_param("ssi", $msg, $status, $id);
    $update->execute();
    
    header("Location: admin_applications.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Rediģēt pieteikumu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow p-4">
            <h4>Rediģēt pieteikumu #<?= $id ?></h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Ziņa / Kontakti</label>
                    <textarea name="message" class="form-control" rows="4"><?= htmlspecialchars($app['message']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Statuss</label>
                    <select name="status" class="form-select">
                        <option value="pending" <?= $app['status'] == 'pending' ? 'selected' : '' ?>>Gaida (Pending)</option>
                        <option value="approved" <?= $app['status'] == 'approved' ? 'selected' : '' ?>>Apstiprināts</option>
                        <option value="rejected" <?= $app['status'] == 'rejected' ? 'selected' : '' ?>>Noraidīts</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Saglabāt</button>
                <a href="admin_applications.php" class="btn btn-link w-100 mt-2 text-decoration-none">Atcelt</a>
            </form>
        </div>
    </div>
</body>
</html>