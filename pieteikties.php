<?php
// Ieslēdzam kļūdu paziņojumus, lai redzētu, kas nobruka (ja nobruks)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Riga');
session_start();

require 'db.php';

// Pārbauda, vai ir ielogojies
if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}

$listing_id = $_GET['listing_id'] ?? $_POST['listing_id'] ?? null;

if (!$listing_id) {
    die("Kļūda: Sludinājuma ID nav norādīts.");
}

// Ja forma ir iesniegta
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phone = trim($_POST['phone'] ?? '');
    $applicant_id = $_SESSION['id_users'];

    // Iegūt īpašnieka ID no saraksta
    $stmt = $mysqli->prepare("SELECT owner_id FROM jb_listings WHERE id_listings = ?");
    $stmt->bind_param("i", $listing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("Kļūda: Sludinājums nav atrasts datubāzē.");
    }

    $owner_id = $row['owner_id'];

    if ($owner_id == 0) {
        die("Kļūda: Īpašnieks nav atrasts šim sludinājumam.");
    }
    
    if ($owner_id == $applicant_id) {
        die("Jūs nevarat iesniegt pieteikumu uz savu sludinājumu.");
    }

    // Pārbauda, vai jau nav pieteicies
    $check = $mysqli->prepare("SELECT id FROM jb_applications WHERE listing_id = ? AND applicant_id = ?");
    $check->bind_param("ii", $listing_id, $applicant_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Jūs jau esat pieteicies uz šo sludinājumu.");
    }
    $check->close();

    // Saglabāt pieteikumu datubāzē
    $insert_stmt = $mysqli->prepare("
        INSERT INTO jb_applications (listing_id, owner_id, applicant_id, message)
        VALUES (?, ?, ?, ?)
    ");

    if (!$insert_stmt) {
        die("SQL kļūda (Prepare failed): " . $mysqli->error);
    }

    $message = !empty($phone) ? $phone : "Nav norādīts";

    $insert_stmt->bind_param("iiis", $listing_id, $owner_id, $applicant_id, $message);
    
    // Mēģinām saglabāt pieteikumu
    if ($insert_stmt->execute()) {
        
        // 1. TIKAI TAD, ja izdevās saglabāt, mēs reģistrējam darbību arhīvā
        logActivity($mysqli, $applicant_id, 'Jauns pieteikums', "Lietotājs pieteicās sludinājumam ID: $listing_id");
        
        // 2. Saglabājam veiksmes ziņu sesijā (lai next.php to varētu parādīt)
        $_SESSION['success_msg'] = "Jūs veiksmīgi pieteicāties sludinājumam!";
        
        // 3. Pāradresējam atpakaļ uz sludinājumiem
        header("Location: next.php");
        exit();

    } else {
        // JA ŠIS PARĀDĀS, TEV IR PROBLĒMA AR TABULU "jb_applications"
        die("Kļūda saglabājot pieteikumu datubāzē: " . $insert_stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pieteikties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>

<body class="p-4 bg-light">
    
<div class="container" style="max-width: 600px; margin-top: 50px;">
    <a href="next.php" class="btn btn-secondary mb-3">Atpakaļ</a>
    
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Pieteikties sludinājumam</h3>
            
            <form method="post">
                <input type="hidden" name="listing_id" value="<?php echo htmlspecialchars($listing_id); ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Jūsu telefona numurs (pēc izvēles)</label>
                    <input type="text" name="phone" class="form-control" placeholder="Piemēram, 20000000">
                    <div class="form-text">Īpašnieks to redzēs savā pieteikumu sarakstā.</div>
                </div>
                
                <button type="submit" class="btn btn-success w-100">Iesniegt pieteikumu</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>