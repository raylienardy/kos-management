<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id_user = $_SESSION['user']['id_user'];
$error = '';
$success = '';

// Jika ada id_booking di GET, ambil detail booking
$booking = null;
if (isset($_GET['id_booking'])) {
    $id_booking = (int)$_GET['id_booking'];
    $stmt = $db->prepare("SELECT b.*, p.id_pembayaran, p.bukti_transfer, p.status_verifikasi,
                          k.nama_kamar, k.harga
                          FROM booking b
                          JOIN pembayaran p ON b.id_booking = p.id_booking
                          JOIN kamar k ON b.id_kamar = k.id_kamar
                          WHERE b.id_booking = :id_booking AND b.id_user = :id_user");
    $stmt->execute([':id_booking' => $id_booking, ':id_user' => $id_user]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$booking) {
        header("Location: riwayat.php");
        exit;
    }
} else {
    // Tampilkan daftar booking pending yang belum diupload bukti (opsional)
    $stmt = $db->prepare("SELECT b.id_booking, k.nama_kamar, b.total_harga, p.bukti_transfer
                          FROM booking b
                          JOIN pembayaran p ON b.id_booking = p.id_booking
                          JOIN kamar k ON b.id_kamar = k.id_kamar
                          WHERE b.id_user = :id_user AND b.status_booking = 'pending' AND p.bukti_transfer IS NULL");
    $stmt->execute([':id_user' => $id_user]);
    $pending_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Proses upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_booking'])) {
    $id_booking = (int)$_POST['id_booking'];
    // Pastikan booking milik user
    $check = $db->prepare("SELECT id_booking FROM booking WHERE id_booking = :id_booking AND id_user = :id_user");
    $check->execute([':id_booking' => $id_booking, ':id_user' => $id_user]);
    if (!$check->fetch()) {
        $error = "Booking tidak valid.";
    } else {
        // Validasi file
        $file = $_FILES['bukti'];
        $allowed = ['image/jpeg', 'image/png', 'application/pdf'];
        $max_size = 2 * 1024 * 1024; // 2 MB
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = "Gagal mengupload file.";
        } elseif (!in_array($file['type'], $allowed)) {
            $error = "Hanya file JPG, PNG, dan PDF yang diizinkan.";
        } elseif ($file['size'] > $max_size) {
            $error = "Ukuran file maksimal 2 MB.";
        } else {
            // Rename dan simpan
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_name = 'bukti_' . $id_booking . '_' . time() . '.' . $ext;
            $dest = "../uploads/pembayaran/" . $new_name;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                // Update bukti_transfer
                $stmt = $db->prepare("UPDATE pembayaran SET bukti_transfer = :file WHERE id_booking = :id_booking");
                $stmt->execute([':file' => $new_name, ':id_booking' => $id_booking]);
                $success = "Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.";
                // Refresh booking detail
                header("Location: pembayaran.php?id_booking=" . $id_booking);
                exit;
            } else {
                $error = "Gagal menyimpan file.";
            }
        }
    }
}


$page_title = 'Pembayaran - KosManagement';
include '../includes/header.php';
?>
<div class="content-wrapper container mt-4" style="max-width: 600px;">
    <h3>Upload Bukti Pembayaran</h3>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($booking): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5><?= htmlspecialchars($booking['nama_kamar']) ?></h5>
                <p>Total: <strong>Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></strong></p>
                <?php if ($booking['bukti_transfer']): ?>
                    <p>Status: 
                        <span class="badge bg-<?= $booking['status_verifikasi'] == 'valid' ? 'success' : ($booking['status_verifikasi'] == 'tidak_valid' ? 'danger' : 'warning') ?>">
                            <?= ucfirst($booking['status_verifikasi']) ?>
                        </span>
                    </p>
                    <p>File bukti: <a href="../uploads/pembayaran/<?= $booking['bukti_transfer'] ?>" target="_blank">Lihat</a></p>
                    <?php if ($booking['status_verifikasi'] == 'tidak_valid'): ?>
                        <div class="alert alert-warning">Pembayaran ditolak. Silakan upload ulang.</div>
                    <?php endif; ?>
                <?php else: ?>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_booking" value="<?= $booking['id_booking'] ?>">
                        <div class="mb-3">
                            <label>Pilih file bukti transfer (JPG/PNG/PDF, max 2MB)</label>
                            <input type="file" name="bukti" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif (isset($pending_list)): ?>
        <p>Pilih booking yang belum diupload bukti:</p>
        <?php foreach ($pending_list as $item): ?>
            <a href="pembayaran.php?id_booking=<?= $item['id_booking'] ?>" class="list-group-item list-group-item-action">
                <?= htmlspecialchars($item['nama_kamar']) ?> - Rp <?= number_format($item['total_harga'], 0, ',', '.') ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>