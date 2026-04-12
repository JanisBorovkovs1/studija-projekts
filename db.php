<?php
date_default_timezone_set('Europe/Riga');

$mysqli = new mysqli("localhost", "u547027111_mvg", "MVGskola1", "u547027111_mvg");

$mysqli->query("SET time_zone = 'Europe/Riga'");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

// Funkcija, lai viegli saglabātu darbības arhīvā
function logActivity($mysqli, $user_id, $action_type, $details) {
    $stmt = $mysqli->prepare("INSERT INTO jb_activity_log (user_id, action_type, details) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iss", $user_id, $action_type, $details);
        $stmt->execute();
    }
}
?>