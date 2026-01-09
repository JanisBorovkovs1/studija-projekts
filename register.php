<?php
require 'db.php';
$email = $_POST['email'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($password !== $confirm) {
    die("Paroles nesakrīt!");
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (email, password) VALUES (?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $email, $hashed);

if ($stmt->execute()) {
    header("Location: index.html");
    exit();
} else {
    echo "Kļūda" . $stmt->error;
}
?>