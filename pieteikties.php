<?php
date_default_timezone_set('Europe/Riga');
session_start();

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

require 'db.php';

$listing_id = $_GET['listing_id'] ?? $_POST['listing_id'] ?? null;

if (!$listing_id) {
    die("Invalid listing.");
}

// Ja forma ir iesniegta
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phone = trim($_POST['phone']);
    $applicant_id = $_SESSION['id_users'];

    $stmt = $mysqli->prepare("SELECT email FROM jb_users WHERE id_users = ?");
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $email = $user['email'];

    // Iegūt īpašnieka ID no saraksta
    $stmt = $mysqli->prepare("SELECT owner_id FROM jb_listings WHERE id_listings = ?");
    $stmt->bind_param("i", $listing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("Listing not found.");
    }

    $owner_id = $row['owner_id'];

    $check = $mysqli->prepare("
    SELECT id FROM jb_applications 
    WHERE listing_id = ? AND applicant_id = ?
    ");

    $check->bind_param("ii", $listing_id, $applicant_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Jūs jau esat pieteicies uz šo sludinājumu.");
    }

    if ($owner_id == 0) {
    die("Īpašnieks nav atrasts šim sludinājumam.");
    }
    
    if ($owner_id == $applicant_id) {
    die("Jūs nevarat iesniegt pieteikumu uz savu sludinājumu.");
    }

    // Saglabāt pieteikumu datubāzē
    $stmt = $mysqli->prepare("
    INSERT INTO jb_applications (listing_id, owner_id, applicant_id, message)
    VALUES (?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Insert prepare failed: " . $mysqli->error);
    }

    $message = !empty($phone) ? $phone : "";

    $stmt->bind_param("iiis", $listing_id, $owner_id, $applicant_id, $message);
    $stmt->execute();

    header("Location: next.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Īre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>

<body class="p-4 bg-light">
<a href="next.php" class="btn btn-secondary mb-3">Atpakaļ</a>
<div class="container">
    <form method="post">
        <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>Tel. nr.</th>
            </tr>
        </thead>
         <tbody>
            <tr>
                <td><input type="number" name="phone" class="form-control" placeholder="Telefona numurs (pēc izvēles)"></td>
            </tr>
        </tbody>
    </table>
     <button type="submit" class="btn btn-success">Iesniegt</button>
    </form>

</div>

</body>
</html>
