<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_kamar = (int)$_GET['id'];
$database = new Database();
$db = $database->getConnection();

// Ambil detail kamar
$stmt = $db->prepare("SELECT * FROM kamar WHERE id_kamar = :id");
$stmt->execute([':id' => $id_kamar]);
$kamar = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kamar) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Kamar tidak ditemukan.</div></div>";
    include 'includes/footer.php';
    exit;
}

// Ambil fasilitas
$stmt_fas = $db->prepare("SELECT f.nama_fasilitas FROM fasilitas f 
                          JOIN kamar_fasilitas kf ON f.id_fasilitas = kf.id_fasilitas 
                          WHERE kf.id_kamar = :id");
$stmt_fas->execute([':id' => $id_kamar]);
$fasilitas = $stmt_fas->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($kamar['nama_kamar']) ?> - KosManagement</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4 content-wrapper">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($kamar['nama_kamar']) ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-md-6">
                <!-- Carousel sederhana (satu foto dulu) -->
                <img src="uploads/kamar/<?= htmlspecialchars($kamar['foto']) ?>" 
                     class="img-fluid rounded" alt="<?= htmlspecialchars($kamar['nama_kamar']) ?>">
            </div>
            <div class="col-md-6">
                <h2><?= htmlspecialchars($kamar['nama_kamar']) ?></h2>
                <h4 class="text-primary">Rp <?= number_format($kamar['harga'], 0, ',', '.') ?> / bulan</h4>
                <p><strong>Ukuran:</strong> <?= htmlspecialchars($kamar['ukuran']) ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?= $kamar['status_kamar'] == 'tersedia' ? 'success' : 'warning' ?>">
                        <?= ucfirst($kamar['status_kamar']) ?>
                    </span>
                </p>
                <p><strong>Deskripsi:</strong></p>
                <p><?= nl2br(htmlspecialchars($kamar['deskripsi'])) ?></p>
                
                <h5>Fasilitas</h5>
                <ul class="list-group list-group-flush mb-3">
                    <?php foreach ($fasilitas as $f): ?>
                        <li class="list-group-item"><?= htmlspecialchars($f) ?></li>
                    <?php endforeach; ?>
                </ul>
                
                <?php if ($kamar['status_kamar'] == 'tersedia'): ?>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'penyewa'): ?>
                        <a href="<?= BASE_URL ?>user/booking.php?id_kamar=<?= $kamar['id_kamar'] ?>" class="btn btn-lg btn-success">Booking Sekarang</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-lg btn-primary">Login untuk Booking</a>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>Kamar Tidak Tersedia</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>