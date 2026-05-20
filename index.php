<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KosManagement</title>
    <!-- Bootstrap CSS CDN (agar cepat) -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <h1>Selamat datang di KosManagement</h1>
        <p>Sistem manajemen kos/kontrakan modern.</p>
        <!-- Nanti di fase 2 akan diisi daftar kamar -->
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>