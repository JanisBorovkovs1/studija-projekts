<?php
session_start();
require 'db.php';
$owner_id = $_SESSION['id_users'];

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}
# Skaitīt neizlasītos pieteikumus
$stmt = $mysqli->prepare("
UPDATE jb_applications 
SET is_read = 1 
WHERE owner_id = ?
");

$stmt->bind_param("i", $owner_id);
$stmt->execute();

$stmt = $mysqli->prepare("
    SELECT a.*, u.email
    FROM jb_applications a
    JOIN jb_users u ON a.applicant_id = u.id_users
    WHERE a.owner_id = ?
    ORDER BY a.created_at DESC
");

$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Jūsu paziņojumi</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="alert alert-info mt-3">
        <strong><?= htmlspecialchars($row['email']) ?></strong> pieteicās uz jūsu sludinājumu.
        <br>
        <small class="text-muted"><?= htmlspecialchars($row['created_at']) ?></small>
        <br>
        <span class="fw-bold">Kontaktinformācija:</span> <?= htmlspecialchars($row['message']) ?>
    </div>
<?php endwhile; ?>