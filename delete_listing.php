<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

$id = $_POST['id'];
$user_id = $_SESSION['id_users'];

// Pārbaudīt, vai jūs esat sludinājuma autors
$stmt = $mysqli->prepare("SELECT owner_id FROM jb_listings WHERE id_listings = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Sludinājums nav atrasts.");
}

// Lai aizliegtu izdzēst citu sludinājumus
if ($row['owner_id'] != $user_id) {
    die("Jums nav tiesību dzēst šo sludinājumu.");
}

$loc_name = $row['location'];
logActivity($mysqli, $user_id, 'Dzēsts sludinājums', "Lietotājs izdzēsa savu sludinājumu: $loc_name (ID: $id)");

// Izdzēst sludinājumu
$stmt = $mysqli->prepare("DELETE FROM jb_listings WHERE id_listings = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: next.php");
exit();
?>