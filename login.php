<?php
session_start();
require 'db.php';

$email = $_POST['login'] ?? '';
$password = $_POST['pass'] ?? '';

$sql = "SELECT * FROM jb_users WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {

        $_SESSION['id_users'] = $row['id_users'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: next.php");
        }
        exit();
    } else {
        echo "Incorrect password";
    }
} else {
    echo "User not found";
}
