<?php
session_start();
require 'db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM jb_listings WHERE id_listings=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$location = $_POST['location'];
$contact = $_POST['contact'];
$price = $_POST['price'];

$stmt = $mysqli->prepare("
UPDATE jb_listings
SET location=?, contact=?, price=?
WHERE id_listings=?
");

$stmt->bind_param("ssii",$location,$contact,$price,$id);
if ($stmt->execute()) {
        // Tagad arhīvā būs redzams: "Izmainīts sludinājums (ID: 15) - Jaunā vieta: Rīga"
        logActivity($mysqli, $_SESSION['id_users'], 'Rediģēts sludinājums', "Adminis izmainīja sludinājumu (ID: $id). Jaunā vieta: $location, jaunā cena: $price, jaunais kontakts: $contact");

        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<form method="post">

<input name="location" value="<?= $row['location'] ?>"><br>
<input name="contact" value="<?= $row['contact'] ?>"><br>
<input name="price" value="<?= $row['price'] ?>"><br>

<button type="submit">Update</button>

</form>
