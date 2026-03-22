<?php
date_default_timezone_set('Europe/Riga');

$mysqli = new mysqli("localhost", "u547027111_mvg", "MVGskola1", "u547027111_mvg");

$mysqli->query("SET time_zone = 'Europe/Riga'");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");