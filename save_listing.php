<?php
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

$sql = "INSERT INTO jb_listings (location, contact, description, price)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssd", $location, $contact, $description, $price);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: next.php"); // back to listings page
exit();