<?php
require 'db.php';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($password !== $confirm) {
    die("Paroles nesakrīt!");
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$role = 'user';

// Pievienojam 'created' kolonnu un NOW() funkciju
$sql = "INSERT INTO jb_users (email, password, role, created) VALUES (?, ?, ?, NOW())";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $email, $hashed, $role);

if ($stmt->execute()) {
    header("Location: index.php");
    exit();
} else {
    echo "Kļūda" . $stmt->error;
}
?>