<?php
session_start();
require 'db.php'; // Šeit ir jābūt mysqli un logActivity definīcijai

// 1. Pārbaude: vai lietotājs ir admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Pieeja liegta.");
}

$id = $_GET['id'] ?? null;

if ($id) {
    // 2. Pirms dzēšanas iegūstam info arhīvam (lai zinātu, ko izdzēsām)
    $info_stmt = $mysqli->prepare("
        SELECT u.email, l.location 
        FROM jb_applications a
        JOIN jb_users u ON a.applicant_id = u.id_users
        JOIN jb_listings l ON a.listing_id = l.id_listings
        WHERE a.id = ?
    ");
    $info_stmt->bind_param("i", $id);
    $info_stmt->execute();
    $info_res = $info_stmt->get_result()->fetch_assoc();

    if ($info_res) {
        $details = "Lietotāja " . $info_res['email'] . " pieteikums uz " . $info_res['location'];
        
        // 3. Dzēšam pieteikumu
        $stmt = $mysqli->prepare("DELETE FROM jb_applications WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // 4. Reģistrējam darbību tikai tad, ja dzēšana izdevās
            if (function_exists('logActivity')) {
                logActivity($mysqli, $_SESSION['id_users'], 'Dzēsts pieteikums', "Adminis izdzēsa: $details");
            }
        }
    }
}

// 5. Atgriežamies atpakaļ
header("Location: admin_applications.php");
exit();