<?php
session_start();
require 'db.php';

// 1. Pārbaudām, vai lietotājs vispār ir ielogojies
if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id_users'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Iegūstam datus un pārbaudām piederību
$stmt = $mysqli->prepare("SELECT * FROM jb_listings WHERE id_listings = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Sludinājums netika atrasts.");
}

// DROŠĪBA: Ja ielogotais lietotājs nav autors un nav admins, liedzam pieeju
if ($row['owner_id'] != $user_id && $_SESSION['role'] !== 'admin') {
    die("Jums nav tiesību rediģēt šo sludinājumu!");
}

// 3. Apstrādājam izmaiņas
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $location = $_POST['location'];
    $contact = $_POST['contact'];
    $price = $_POST['price'];
    $description = $_POST['description'] ?? '';

    $update_stmt = $mysqli->prepare("
        UPDATE jb_listings 
        SET location = ?, contact = ?, price = ?, description = ? 
        WHERE id_listings = ? AND (owner_id = ? OR 'admin' = ?)
    ");
    
    // Šeit mēs drošības pēc vēlreiz pārbaudām owner_id vaicājumā
    $admin_check = $_SESSION['role'];
    $update_stmt->bind_param("ssdsiis", $location, $contact, $price, $description, $id, $user_id, $admin_check);

    if ($update_stmt->execute()) {
        // Ierakstām arhīvā, ka autors pats veica izmaiņas
        logActivity($mysqli, $user_id, 'Rediģēts sludinājums', "Lietotājs laboja savu sludinājumu ID: $id");

        header("Location: next.php");
        exit();
    } else {
        $error = "Kļūda saglabājot: " . $mysqli->error;
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Rediģēt manu sludinājumu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container" style="max-width: 600px;">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4">Rediģēt sludinājumu</h2>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Atrašanās vieta</label>
                <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($row['location']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Kontakti</label>
                <input type="email" name="contact" class="form-control" value="<?= htmlspecialchars($row['contact']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Cena (EUR/h)</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($row['price']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Apraksts</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($row['description'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success w-100">Saglabāt</button>
                <a href="next.php" class="btn btn-secondary w-100">Atcelt</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>