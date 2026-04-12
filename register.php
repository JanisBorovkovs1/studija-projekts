<?php
session_start();
require 'db.php';

// Ja lietotājs jau ir ielogojies, sūtām uz galveno lapu
if (isset($_SESSION['id_users'])) {
    header("Location: next.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // 1. Pārbaudām vai paroles sakrīt
    if ($password !== $password_confirm) {
        $error = "Paroles nesakrīt!";
    } else {
        // 2. Pārbaudām vai e-pasts jau nav aizņemts
        $check_stmt = $mysqli->prepare("SELECT id_users FROM jb_users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Šāds e-pasts jau ir reģistrēts!";
        } else {
            // 3. Šifrējam paroli un saglabājam lietotāju
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; // Noklusētā loma jaunam lietotājam

            $stmt = $mysqli->prepare("INSERT INTO jb_users (email, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = "Konts izveidots veiksmīgi! Tagad vari pieslēgties.";
            } else {
                $error = "Kļūda saglabājot datus.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studiju Īre | Reģistrēties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .login-info {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); /* Zils tonis reģistrācijai */
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-form {
            padding: 40px;
        }
        .btn-primary {
            background: #0d6efd;
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        .studio-tag {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="row login-container">
                
                <div class="col-md-6 login-info d-none d-md-flex">
                    <div class="studio-tag">Pievienojies kopienai</div>
                    <h1 class="fw-bold mb-3">Sāc savu radošo ceļu šeit.</h1>
                    <p class="lead opacity-75">Izveido kontu, lai rezervētu labākās studijas saviem projektiem un sazinātos ar telpu īpašniekiem.</p>
                    <div class="mt-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="me-3 fs-4">✅</span>
                            <span>Piekļuve visiem sludinājumiem</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="me-3 fs-4">✅</span>
                            <span>Ātra pieteikšanās īrei</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="me-3 fs-4">✅</span>
                            <span>Saziņa ar studiju vadītājiem</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 login-form">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Izveidot kontu</h2>
                        <p class="text-muted">Aizpildi datus, lai reģistrētos</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success py-2 small"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">E-pasts</label>
                            <input type="email" name="email" class="form-control" placeholder="vards@pasts.lv" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Parole</label>
                            <input type="password" name="password" class="form-control" placeholder="Izdomā paroli" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Atkārtot paroli</label>
                            <input type="password" name="password_confirm" class="form-control" placeholder="Ievadi vēlreiz" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">Reģistrēties</button>
                    </form>

                    <div class="text-center">
                        <span class="text-muted small">Jau esi reģistrējies?</span>
                        <a href="index.php" class="small fw-bold text-decoration-none text-primary">Pieslēgties</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>