<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$id = $_GET['id'] ?? null;
$admin_id = $_SESSION['id_users'];

if ($id) {
    // 1. SOLIS: Iegūstam informāciju par sludinājumu PIRMS tā dzēšanas
    $info_stmt = $mysqli->prepare("SELECT location FROM jb_listings WHERE id_listings = ?");
    $info_stmt->bind_param("i", $id);
    $info_stmt->execute();
    $info_result = $info_stmt->get_result();
    $listing = $info_result->fetch_assoc();
    
    $location_name = $listing['location'] ?? "Nezināma vieta";

    // 2. SOLIS: Reģistrējam darbību arhīvā (izmantojot funkciju no db.php)
    // logActivity($mysqli, lietotāja_id, darbība, detaļas)
    logActivity($mysqli, $admin_id, 'Dzēsts sludinājums', "Adminis izdzēsa sludinājumu ID: $id (Vieta: $location_name)");

    // 3. SOLIS: Tagad droši dzēšam pašu ierakstu
    $stmt = $mysqli->prepare("DELETE FROM jb_listings WHERE id_listings = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: admin_dashboard.php");
exit();
?>