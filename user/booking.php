<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Ambil data kamar jika id_kamar dikirim
if (!isset($_GET['id_kamar']) || empty($_GET['id_kamar'])) {
    header("Location: ../index.php");
    exit;
}
$id_kamar = (int)$_GET['id_kamar'];
$stmt = $db->prepare("SELECT * FROM kamar WHERE id_kamar = :id AND status_kamar = 'tersedia'");
$stmt->execute([':id' => $id_kamar]);
$kamar = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$kamar) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Kamar tidak tersedia.</div></div>";
    include '../includes/footer.php';
    exit;
}

$error = '';
$success = false;
$booking_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $durasi = (int)$_POST['durasi_sewa'];

    // Validasi sederhana
    if (empty($tanggal_masuk) || $durasi < 1) {
        $error = 'Tanggal masuk dan durasi minimal 1 bulan wajib diisi.';
    } else {
        $id_user = $_SESSION['user']['id_user'];
        $total = $kamar['harga'] * $durasi;

        // Insert booking
        $stmt = $db->prepare("INSERT INTO booking (id_user, id_kamar, tanggal_masuk, durasi_sewa, total_harga)
                              VALUES (:id_user, :id_kamar, :tanggal_masuk, :durasi, :total)");
        $stmt->execute([
            ':id_user' => $id_user,
            ':id_kamar' => $id_kamar,
            ':tanggal_masuk' => $tanggal_masuk,
            ':durasi' => $durasi,
            ':total' => $total
        ]);
        $booking_id = $db->lastInsertId();

        // Insert pembayaran awal (kosong, menunggu upload)
        $stmt2 = $db->prepare("INSERT INTO pembayaran (id_booking, metode_pembayaran) VALUES (:id_booking, 'transfer')");
        $stmt2->execute([':id_booking' => $booking_id]);

        // Ubah status kamar jadi 'dipesan'
        $db->prepare("UPDATE kamar SET status_kamar = 'dipesan' WHERE id_kamar = :id")->execute([':id' => $id_kamar]);

        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Kamar - KosManagement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4" style="max-width: 600px;">
        <?php if ($success): ?>
            <div class="alert alert-success">
                <h5>Booking berhasil!</h5>
                <p>Total biaya: <strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></p>
                <p>Silakan lanjutkan ke pembayaran dengan transfer ke:</p>
                <p><strong>Bank BCA 1234567890 a.n. KosManagement</strong></p>
                <a href="pembayaran.php?id_booking=<?= $booking_id ?>" class="btn btn-primary">Upload Bukti Pembayaran</a>
            </div>
        <?php else: ?>
            <h3>Booking Kamar: <?= htmlspecialchars($kamar['nama_kamar']) ?></h3>
            <p>Harga per bulan: <strong>Rp <?= number_format($kamar['harga'], 0, ',', '.') ?></strong></p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Durasi Sewa (bulan)</label>
                    <input type="number" name="durasi_sewa" class="form-control" min="1" required>
                </div>
                <button type="submit" class="btn btn-success">Booking Sekarang</button>
            </form>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>