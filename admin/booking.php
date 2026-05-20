<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';
$db = (new Database())->getConnection();

$filter = $_GET['status'] ?? '';
$where = '';
$params = [];
if ($filter) {
    $where = "WHERE b.status_booking = :status";
    $params[':status'] = $filter;
}
$stmt = $db->prepare("SELECT b.*, u.nama AS nama_user, k.nama_kamar 
                      FROM booking b JOIN users u ON b.id_user=u.id_user 
                      JOIN kamar k ON b.id_kamar=k.id_kamar 
                      $where ORDER BY b.tanggal_booking DESC");
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Booking - Admin</title>
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
                    <li class="nav-item"><a class="nav-link active" href="booking.php">Data Booking</a></li>
                    <li class="nav-item"><a class="nav-link" href="verifikasi.php">Verifikasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php">Kelola User</a></li>
                    <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
                </ul>
            </div>
        </nav>
        <main class="col-md-10 ms-sm-auto px-md-4">
            <h2 class="mt-3">Data Booking</h2>
            <div class="mb-3">
                <a href="booking.php" class="btn btn-sm btn-outline-secondary">Semua</a>
                <a href="?status=pending" class="btn btn-sm btn-warning">Pending</a>
                <a href="?status=diterima" class="btn btn-sm btn-success">Diterima</a>
                <a href="?status=ditolak" class="btn btn-sm btn-danger">Ditolak</a>
            </div>
            <table class="table table-bordered">
                <thead><tr><th>ID Booking</th><th>User</th><th>Kamar</th><th>Tgl Masuk</th><th>Durasi</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= $b['id_booking'] ?></td>
                        <td><?= htmlspecialchars($b['nama_user']) ?></td>
                        <td><?= htmlspecialchars($b['nama_kamar']) ?></td>
                        <td><?= $b['tanggal_masuk'] ?></td>
                        <td><?= $b['durasi_sewa'] ?> bln</td>
                        <td>Rp <?= number_format($b['total_harga'],0,',','.') ?></td>
                        <td><span class="badge bg-<?= $b['status_booking']=='diterima'?'success':($b['status_booking']=='ditolak'?'danger':'warning') ?>"><?= $b['status_booking'] ?></span></td>
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