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
    <div class="alert alert-info mt-3 shadow-sm" style="border-radius: 10px; border-left: 5px solid #0d6efd;">
        <div class="d-flex justify-content-between">
            <strong><?= htmlspecialchars($row['email']) ?></strong>
            <small class="text-muted"><?= htmlspecialchars($row['created_at']) ?></small>
        </div>
        
        <p class="mb-1">Pieteicās uz jūsu sludinājumu.</p>

        <?php if (!empty($row['message'])): ?>
            <div class="mt-2">
                <span class="badge bg-dark">Telefons:</span> 
                <span class="ms-1 fw-bold text-primary"><?= htmlspecialchars($row['message']) ?></span>
            </div>
        <?php else: ?>
            <small class="text-muted italic text-decoration-underline">Telefona numurs netika norādīts.</small>
        <?php endif; ?>
    </div>
<?php endwhile; ?>