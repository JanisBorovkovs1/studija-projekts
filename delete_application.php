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
        // 1. Iegūstam informāciju PAR pieteikumu, lai to varētu saglabāt arhīvā
        $info_stmt = $mysqli->prepare("SELECT message, listing_id FROM jb_applications WHERE id = ? AND applicant_id = ?");
        $info_stmt->bind_param("ii", $app_id, $applicant_id);
        $info_stmt->execute();
        $info_result = $info_stmt->get_result();
        
        if ($info = $info_result->fetch_assoc()) {
            $msg = $info['message'];
            $lst_id = $info['listing_id'];
            
            // 2. Ierakstām arhīvā, KAS tika izdzēsts
            $details = "Atcelts pieteikums uz sludinājumu ID: $lst_id. Atstātā ziņa pirms dzēšanas: '$msg'";
            logActivity($mysqli, $applicant_id, 'Atcelts pieteikums', $details);

            // 3. Dzēšam ārā pašu pieteikumu
            $stmt = $mysqli->prepare("DELETE FROM jb_applications WHERE id = ? AND applicant_id = ?");
            $stmt->bind_param("ii", $app_id, $applicant_id);
            $stmt->execute();
        }
    }
}

// Atgriežamies atpakaļ uz pieteikumu sarakstu
header("Location: my_applications.php");
exit();
?>