<?php
require 'db.php';
session_start();

$email = $_POST['email'];
$password = $_POST['password'];

if (empty($email)) {
        $error .= '<p class="error">Please enter email.</p>';
    }

    if (empty($password)) {
        $error .= '<p class="error">Please enter your password.</p>';
    }

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

