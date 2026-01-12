<?php
$mysqli = new mysqli("localhost", "u547027111_mvg", "MVGskola1", "u547027111_mvg");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");