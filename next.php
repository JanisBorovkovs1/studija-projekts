<?php
session_start();

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
    exit();
}
$conn = new mysqli("localhost", "u547027111_mvg", "MVGskola1", "u547027111_mvg");
$result = $conn->query("SELECT * FROM jb_listings ORDER BY created_at DESC");

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Īre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4 bg-light">
<a href="logout.php" class="btn btn-danger">Logout</a>
<div class="container fade-in">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>Atrašanās vieta</th>
                <th>Īpašnieka kontaktinformācija</th>
                <th>Vērtējums</th>
                <th>Cena</th>
                <th>
                <form action="izveidot.php">
                    <button type="submit" class="btn btn-success btn-sm">Izveidot savu!</button>
                </form>

                </th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['contact']) ?></td>
                <td>⭐</td>
                <td><?= htmlspecialchars($row['price']) ?> EUR/h</td>
                <td>
                    <form action="pieteikties.php">
                        <button type="submit" class="btn btn-success btn-sm">Pieteikties</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>

                </form>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php $conn->close(); ?>

</body>
</html>
