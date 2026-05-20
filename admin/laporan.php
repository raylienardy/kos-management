<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';
$db = (new Database())->getConnection();

// Penghuni aktif: booking diterima, dan tanggal_masuk + durasi masih berlaku hingga sekarang
$aktif = $db->query("SELECT b.id_booking, u.nama AS nama_user, k.nama_kamar, b.tanggal_masuk, b.durasi_sewa, 
                    DATE_ADD(b.tanggal_masuk, INTERVAL b.durasi_sewa MONTH) AS akhir_sewa
                    FROM booking b 
                    JOIN users u ON b.id_user=u.id_user 
                    JOIN kamar k ON b.id_kamar=k.id_kamar 
                    WHERE b.status_booking='diterima' 
                    AND DATE_ADD(b.tanggal_masuk, INTERVAL b.durasi_sewa MONTH) >= CURDATE()")->fetchAll(PDO::FETCH_ASSOC);

// Laporan bulanan: total pemasukan per bulan
$laporan_bulanan = $db->query("SELECT DATE_FORMAT(tanggal_booking, '%Y-%m') AS bulan, SUM(total_harga) AS total 
                               FROM booking WHERE status_booking='diterima' 
                               GROUP BY bulan ORDER BY bulan DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="kamar.php">Kelola Kamar</a></li>
                    <li class="nav-item"><a class="nav-link" href="booking.php">Data Booking</a></li>
                    <li class="nav-item"><a class="nav-link" href="verifikasi.php">Verifikasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php">Kelola User</a></li>
                    <li class="nav-item"><a class="nav-link active" href="laporan.php">Laporan</a></li>
                </ul>
            </div>
        </nav>
        <main class="col-md-10 ms-sm-auto px-md-4">
            <h2 class="mt-3">Laporan</h2>
            <h4>Penghuni Aktif</h4>
            <table class="table table-bordered">
                <thead><tr><th>User</th><th>Kamar</th><th>Tanggal Masuk</th><th>Durasi</th><th>Akhir Sewa</th></tr></thead>
                <tbody>
                <?php foreach ($aktif as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['nama_user']) ?></td>
                        <td><?= htmlspecialchars($a['nama_kamar']) ?></td>
                        <td><?= $a['tanggal_masuk'] ?></td>
                        <td><?= $a['durasi_sewa'] ?> bln</td>
                        <td><?= $a['akhir_sewa'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h4>Pemasukan Bulanan</h4>
            <table class="table table-bordered">
                <thead><tr><th>Bulan</th><th>Total Pemasukan</th></tr></thead>
                <tbody>
                <?php foreach ($laporan_bulanan as $lb): ?>
                    <tr>
                        <td><?= $lb['bulan'] ?></td>
                        <td>Rp <?= number_format($lb['total'],0,',','.') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>