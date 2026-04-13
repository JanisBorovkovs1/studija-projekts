<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require 'db.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

$location = $_POST['location'];
$contact = $_POST['contact'];
$description = $_POST['description'];
$price = $_POST['price'];
$owner_id = $_SESSION['id_users'];

$sql = "INSERT INTO jb_listings (location, contact, description, price, owner_id, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sssdi", $location, $contact, $description, $price, $owner_id);

if ($stmt->execute()) {
    $jaunais_id = $mysqli->insert_id; 

    logActivity($mysqli, $owner_id, 'Izveidots sludinājums', "Lietotājs izveidoja jaunu vietu (ID: $jaunais_id): " . $location);
}

$stmt->close();

header("Location: next.php");
exit();