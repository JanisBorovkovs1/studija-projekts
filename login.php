<?php
session_start();
require 'db.php';

$email = $_POST['login'] ?? '';
$password = $_POST['pass'] ?? '';

if (empty($email)) {
        $error .= '<p class="error">Please enter email.</p>';
    }

    if (empty($password)) {
        $error .= '<p class="error">Please enter your password.</p>';
    }

$sql = "SELECT * FROM jb_users WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    if (password_verify($password, $row['password'])) {
        
        $_SESSION['id_users'] = $row['id'];
        $_SESSION['email'] = $row['email'];

        echo '<pre>';
        var_dump($_SESSION);
        echo '</pre>';
        exit();
        //$_SESSION['id_users'] = $row['id'];
        //$_SESSION['email'] = $row['email'];

        //header("Location: next.php");
        //exit();

    } else {
        echo "Incorrect password";
    }

} else {
    echo "User not found";
}

