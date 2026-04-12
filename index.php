<?php
session_start();
require 'db.php';

// Ja lietotājs jau ir ielogojies, sūtām uz galveno lapu
if (isset($_SESSION['id_users'])) {
    header("Location: next.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id_users, password, role FROM jb_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['id_users'] = $user['id_users'];
            $_SESSION['role'] = $user['role'];
            header("Location: next.php");
            exit();
        } else {
            $error = "Nepareiza parole!";
        }
    } else {
        $error = "Lietotājs ar šādu e-pastu neeksistē!";
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studiju Īre | Pieslēgties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
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
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
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
            background: rgba(255,255,255,0.1);
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
                    <div class="studio-tag">Premium Studiju Tīkls</div>
                    <h1 class="fw-bold mb-3">Tava radošā telpa.</h1>
                    <p class="lead opacity-75">Rezervē profesionālas studijas podkāstiem, mūzikas ierakstiem un radošajām sesijām dažu minūšu laikā.</p>
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2">🎙️ **Podkāstu aprīkojums**</li>
                        <li class="mb-2">🎹 **Mūzikas instrumenti**</li>
                        <li class="mb-2">🎧 **Skaņas izolācija**</li>
                    </ul>
                </div>

                <div class="col-md-6 login-form">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Pieslēgties</h2>
                        <p class="text-muted">Ievadi savus datus, lai turpinātu</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">E-pasts</label>
                            <input type="email" name="email" class="form-control" placeholder="vards@pasts.lv" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Parole</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">Ienākt sistēmā</button>
                    </form>

                    <div class="text-center">
                        <span class="text-muted small">Vēl neesi biedrs?</span>
                        <a href="register.php" class="small fw-bold text-decoration-none text-primary">Izveidot kontu</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>