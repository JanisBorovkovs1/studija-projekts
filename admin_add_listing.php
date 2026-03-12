<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$location = $_POST['location'];
$contact = $_POST['contact'];
$price = $_POST['price'];
$owner = $_SESSION['id_users'];

$stmt = $mysqli->prepare("
INSERT INTO jb_listings (location, contact, price, owner_id)
VALUES (?, ?, ?, ?)
");
$stmt->bind_param("ssii", $location, $contact, $price, $owner);
$stmt->execute();