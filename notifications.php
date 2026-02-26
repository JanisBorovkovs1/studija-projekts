<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

$owner_id = $_SESSION['id_users'];

$stmt = $mysqli->prepare("
    SELECT a.*, u.username 
    FROM jb_applications a
    JOIN jb_users u ON a.applicant_id = u.id_users
    WHERE a.owner_id = ?
    ORDER BY a.created_at DESC
");

$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Your Notifications</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <div>
        <strong><?= htmlspecialchars($row['username']) ?></strong>
        applied to your listing.
        <br>
        <?= htmlspecialchars($row['created_at']) ?>
        <hr>
    </div>
<?php endwhile; ?>