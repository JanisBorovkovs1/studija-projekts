<?php
require 'db.php';
$email = $_POST['email'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($password !== $confirm) {
    die("Paroles nesakrīt!");
}


$sql = "INSERT INTO users (email, password) VALUES (?, ?)";