<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';
$db = (new Database())->getConnection();

// Ringkasan
$kamar_tersedia = $db->query("SELECT COUNT(*) FROM kamar WHERE status_kamar='tersedia'")->fetchColumn();
$booking_pending = $db->query("SELECT COUNT(*) FROM booking WHERE status_booking='pending'")->fetchColumn();
$pemasukan_bulan_ini = $db->query("SELECT COALESCE(SUM(total_harga),0) FROM booking WHERE status_booking='diterima' AND MONTH(tanggal_booking)=MONTH(CURDATE()) AND YEAR(tanggal_booking)=YEAR(CURDATE())")->fetchColumn();
$total_user = $db->query("SELECT COUNT(*) FROM users WHERE role='penyewa'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - KosManagement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="kamar.php">Kelola Kamar</a></li>
                        <li class="nav-item"><a class="nav-link" href="booking.php">Data Booking</a></li>
                        <li class="nav-item"><a class="nav-link" href="verifikasi.php">Verifikasi Pembayaran</a></li>
                        <li class="nav-item"><a class="nav-link" href="users.php">Kelola User</a></li>
                        <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
                    </ul>
                </div>
            </nav>
            <main class="col-md-10 ms-sm-auto px-md-4">
                <h2 class="mt-3">Dashboard Admin</h2>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?= $kamar_tersedia ?></h5>
                                <p class="card-text">Kamar Tersedia</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?= $booking_pending ?></h5>
                                <p class="card-text">Booking Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Rp <?= number_format($pemasukan_bulan_ini,0,',','.') ?></h5>
                                <p class="card-text">Pemasukan Bulan Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?= $total_user ?></h5>
                                <p class="card-text">Penyewa Terdaftar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>