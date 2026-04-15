<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') die("Access denied.");

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $mysqli->prepare("UPDATE jb_applications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: admin_applications.php");
exit();