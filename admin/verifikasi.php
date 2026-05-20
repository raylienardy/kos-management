<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';
$db = (new Database())->getConnection();

// Proses verifikasi
if (isset($_GET['action']) && isset($_GET['id_booking'])) {
    $id_booking = (int)$_GET['id_booking'];
    $action = $_GET['action']; // 'valid' atau 'tolak'
    try {
        $db->beginTransaction();
        if ($action === 'valid') {
            $status_booking = 'diterima';
            $status_verifikasi = 'valid';
            // Status kamar jadi terisi
            $stmt = $db->prepare("UPDATE kamar SET status_kamar='terisi' WHERE id_kamar=(SELECT id_kamar FROM booking WHERE id_booking=?)");
            $stmt->execute([$id_booking]);
        } else { // tolak
            $status_booking = 'ditolak';
            $status_verifikasi = 'tidak_valid';
            // Kembalikan kamar jadi tersedia
            $stmt = $db->prepare("UPDATE kamar SET status_kamar='tersedia' WHERE id_kamar=(SELECT id_kamar FROM booking WHERE id_booking=?)");
            $stmt->execute([$id_booking]);
        }
        $updateBooking = $db->prepare("UPDATE booking SET status_booking=? WHERE id_booking=?");
        $updateBooking->execute([$status_booking, $id_booking]);
        $updatePembayaran = $db->prepare("UPDATE pembayaran SET status_verifikasi=? WHERE id_booking=?");
        $updatePembayaran->execute([$status_verifikasi, $id_booking]);
        $db->commit();
        $msg = '<div class="alert alert-success">Verifikasi berhasil.</div>';
    } catch (Exception $e) {
        $db->rollBack();
        $msg = '<div class="alert alert-danger">Gagal: '.$e->getMessage().'</div>';
    }
}

// Ambil booking pending yang sudah upload bukti
$stmt = $db->query("SELECT b.id_booking, u.nama AS nama_user, k.nama_kamar, b.tanggal_masuk, b.durasi_sewa, b.total_harga, p.bukti_transfer 
                    FROM booking b 
                    JOIN users u ON b.id_user=u.id_user 
                    JOIN kamar k ON b.id_kamar=k.id_kamar 
                    JOIN pembayaran p ON b.id_booking=p.id_booking 
                    WHERE b.status_booking='pending' AND p.bukti_transfer IS NOT NULL 
                    ORDER BY b.tanggal_booking DESC");
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar_admin.php'; ?>
        <main class="col-md-10 ms-sm-auto px-md-4">
            <h2 class="mt-3">Verifikasi Pembayaran</h2>
            <?= $msg ?? '' ?>
            <table class="table table-bordered">
                <thead><tr><th>ID Booking</th><th>User</th><th>Kamar</th><th>Tgl Masuk</th><th>Durasi</th><th>Total</th><th>Bukti</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($pending as $p): ?>
                    <tr>
                        <td><?= $p['id_booking'] ?></td>
                        <td><?= htmlspecialchars($p['nama_user']) ?></td>
                        <td><?= htmlspecialchars($p['nama_kamar']) ?></td>
                        <td><?= $p['tanggal_masuk'] ?></td>
                        <td><?= $p['durasi_sewa'] ?> bln</td>
                        <td>Rp <?= number_format($p['total_harga'],0,',','.') ?></td>
                        <td><a href="../uploads/pembayaran/<?= $p['bukti_transfer'] ?>" target="_blank">Lihat</a></td>
                        <td>
                            <a href="?action=valid&id_booking=<?= $p['id_booking'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Verifikasi valid?')">Valid</a>
                            <a href="?action=tolak&id_booking=<?= $p['id_booking'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pembayaran?')">Tolak</a>
                        </td>
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