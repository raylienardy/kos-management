<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';

$id_user = $_SESSION['user']['id_user'];
$database = new Database();
$db = $database->getConnection();

// Hitung ringkasan
$stmt = $db->prepare("SELECT 
    COUNT(CASE WHEN b.status_booking = 'pending' THEN 1 END) AS pending,
    COUNT(CASE WHEN b.status_booking = 'diterima' THEN 1 END) AS aktif,
    COUNT(CASE WHEN b.status_booking = 'ditolak' THEN 1 END) AS ditolak
    FROM booking b WHERE b.id_user = :id_user");
$stmt->execute([':id_user' => $id_user]);
$summary = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penyewa - KosManagement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4">
        <h2>Selamat datang, <?= htmlspecialchars($_SESSION['user']['nama']) ?></h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= $summary['pending'] ?? 0 ?></h5>
                        <p class="card-text">Booking Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= $summary['aktif'] ?? 0 ?></h5>
                        <p class="card-text">Booking Aktif (Diterima)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= $summary['ditolak'] ?? 0 ?></h5>
                        <p class="card-text">Ditolak</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-3">
                <a href="riwayat.php" class="btn btn-outline-primary w-100">Lihat Riwayat Booking</a>
            </div>
            <div class="col-md-3">
                <a href="profil.php" class="btn btn-outline-secondary w-100">Edit Profil</a>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>