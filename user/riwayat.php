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

$stmt = $db->prepare("SELECT b.*, k.nama_kamar, k.foto, p.status_verifikasi,
                      p.bukti_transfer
                      FROM booking b
                      JOIN kamar k ON b.id_kamar = k.id_kamar
                      JOIN pembayaran p ON b.id_booking = p.id_booking
                      WHERE b.id_user = :id_user
                      ORDER BY b.tanggal_booking DESC");
$stmt->execute([':id_user' => $id_user]);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - KosManagement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4">
        <h3>Riwayat Booking</h3>
        <?php if ($riwayat): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Kamar</th>
                        <th>Tanggal Masuk</th>
                        <th>Durasi</th>
                        <th>Total</th>
                        <th>Status Booking</th>
                        <th>Pembayaran</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayat as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nama_kamar']) ?></td>
                        <td><?= date('d/m/Y', strtotime($r['tanggal_masuk'])) ?></td>
                        <td><?= $r['durasi_sewa'] ?> bulan</td>
                        <td>Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                        <td>
                            <span class="badge bg-<?= $r['status_booking'] == 'diterima' ? 'success' : ($r['status_booking'] == 'ditolak' ? 'danger' : 'warning') ?>">
                                <?= ucfirst($r['status_booking']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $r['status_verifikasi'] == 'valid' ? 'success' : ($r['status_verifikasi'] == 'tidak_valid' ? 'danger' : 'secondary') ?>">
                                <?= ucfirst(str_replace('_', ' ', $r['status_verifikasi'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($r['bukti_transfer']): ?>
                                <a href="../uploads/pembayaran/<?= $r['bukti_transfer'] ?>" target="_blank">Lihat</a>
                            <?php else: ?>
                                <a href="pembayaran.php?id_booking=<?= $r['id_booking'] ?>">Upload</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Belum ada riwayat booking.</div>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>