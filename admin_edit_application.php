<?php
session_start();
require 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Pieeja liegta.");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: admin_applications.php");
    exit();
}

// Iegūstam esošos datus
$stmt = $mysqli->prepare("SELECT a.*, u.email FROM jb_applications a JOIN jb_users u ON a.applicant_id = u.id_users WHERE a.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

if (!$app) die("Pieteikums netika atrasts.");

// Apstrādājam saglabāšanu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_message = $_POST['message'];
    
    $update_stmt = $mysqli->prepare("UPDATE jb_applications SET message = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_message, $id);
    
    if ($update_stmt->execute()) {
        if (function_exists('logActivity')) {
            logActivity($mysqli, $_SESSION['id_users'], 'Labots pieteikums', "Labots ziņojums pieteikumam ID: $id");
        }
        header("Location: admin_applications.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Labot pieteikumu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Labot pieteikuma kontaktinformāciju</h5>
            </div>
            <div class="card-body">
                <p>Pieteicējs: <strong><?= htmlspecialchars($app['email']) ?></strong></p>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Ziņojums / Kontakti:</label>
                        <textarea name="message" class="form-control" rows="6"><?= htmlspecialchars($app['message']) ?></textarea>
                        <div class="form-text">Šeit vari izlabot lietotāja ievadītos kontaktus vai ziņu.</div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Saglabāt izmaiņas</button>
                        <a href="admin_applications.php" class="btn btn-secondary">Atcelt</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>