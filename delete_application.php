<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $app_id = $_POST['app_id'] ?? null;
    $applicant_id = $_SESSION['id_users'];

    if ($app_id) {
        // Dzēšam TIKAI tad, ja pieteikums pieder šim lietotājam
        $stmt = $mysqli->prepare("DELETE FROM jb_applications WHERE id = ? AND applicant_id = ?");
        $stmt->bind_param("ii", $app_id, $applicant_id);
        $stmt->execute();
    }
}

// Atgriežamies atpakaļ uz pieteikumu sarakstu
header("Location: my_applications.php");
exit();
?>