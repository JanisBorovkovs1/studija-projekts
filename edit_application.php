<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

$applicant_id = $_SESSION['id_users'];
$app_id = $_GET['id'] ?? null;

if (!$app_id) {
    die("Pieteikums nav norādīts.");
}

// Iegūstam pieteikumu, lai pārliecinātos, ka tas pieder šim lietotājam
$stmt = $mysqli->prepare("SELECT message FROM jb_applications WHERE id = ? AND applicant_id = ?");
$stmt->bind_param("ii", $app_id, $applicant_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

if (!$application) {
    die("Pieteikums nav atrasts vai jums nav tiesību to labot.");
}

// Ja forma tiek iesniegta
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_message = trim($_POST['message']);
    
    $update_stmt = $mysqli->prepare("UPDATE jb_applications SET message = ? WHERE id = ? AND applicant_id = ?");
    $update_stmt->bind_param("sii", $new_message, $app_id, $applicant_id);
    $update_stmt->execute();
    
    header("Location: my_applications.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labot pieteikumu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>
<body class="p-4 bg-light">

<div class="container fade-in" style="max-width: 600px;">
    <div class="card shadow-sm mt-5">
        <div class="card-body p-4">
            <h3 class="card-title fw-bold mb-4">Labot pieteikuma ziņojumu</h3>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Tavs ziņojums vai kontaktinformācija</label>
                    <textarea name="message" class="form-control" rows="5" required><?= htmlspecialchars($application['message']) ?></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Saglabāt izmaiņas</button>
                    <a href="my_applications.php" class="btn btn-outline-secondary">Atcelt</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>