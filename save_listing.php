<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "u547027111_mvg", "MVGskola1", "u547027111_mvg");

if ($conn->connect_error) {
    die("Connection failed");
}

$location = $_POST['location'];
$contact = $_POST['contact'];
$description = $_POST['description'];
$price = $_POST['price'];
$owner_id = $_SESSION['id_users'];

$sql = "INSERT INTO jb_listings (location, contact, description, price, owner_id, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssd", $location, $contact, $description, $price, $owner_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: next.php");
exit();