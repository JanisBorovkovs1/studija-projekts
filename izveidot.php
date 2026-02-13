<?php
session_start();

if (!isset($_SESSION['id_users'])) {
    header("Location: index.php");
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

<div class="container">
    <form action="save_listing.php" method="post">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>Atrašanās vieta</th>
                <th>Īpašnieka kontaktinformācija</th>
                <th>Apraksts</th>
                <th>Cena</th>
                </th>
            </tr>
        </thead>
         <tbody>
            <tr>
                <td><input type="text" name="location" class="form-control" required></td>
                <td><input type="email" name="contact" class="form-control" required></td>
                <td><input type="text" name="description" class="form-control" required></td>
                <td><input type="number" name="price" step="0.01" class="form-control" required></td>
            </tr>
        </tbody>
    </table>
     <button type="submit" class="btn btn-success">Iesniegt</button>
    </form>

</div>

</body>
</html>
