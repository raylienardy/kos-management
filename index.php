<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Pencarian & filter
$where = "WHERE k.status_kamar = 'tersedia'";
$params = [];

if (isset($_GET['cari']) && !empty($_GET['keyword'])) {
    $keyword = "%" . $_GET['keyword'] . "%";
    $where .= " AND (k.nama_kamar LIKE :keyword OR k.deskripsi LIKE :keyword OR k.ukuran LIKE :keyword)";
    $params[':keyword'] = $keyword;
}
if (isset($_GET['harga_min']) && $_GET['harga_min'] !== '' && isset($_GET['harga_max']) && $_GET['harga_max'] !== '') {
    $where .= " AND k.harga BETWEEN :min AND :max";
    $params[':min'] = (int)$_GET['harga_min'];
    $params[':max'] = (int)$_GET['harga_max'];
}

// Ambil data kamar beserta fasilitasnya (group_concat untuk menampilkan fasilitas per kamar)
$sql = "SELECT k.*, 
               GROUP_CONCAT(f.nama_fasilitas SEPARATOR ', ') AS fasilitas_list
        FROM kamar k
        LEFT JOIN kamar_fasilitas kf ON k.id_kamar = kf.id_kamar
        LEFT JOIN fasilitas f ON kf.id_fasilitas = f.id_fasilitas
        $where
        GROUP BY k.id_kamar
        ORDER BY k.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$kamar_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KosManagement - Daftar Kamar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-3">Daftar Kamar Tersedia</h2>
        
        <!-- Form Pencarian & Filter -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" placeholder="Cari nama, lokasi..." 
                       value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <input type="number" name="harga_min" class="form-control" placeholder="Harga min" 
                       value="<?= htmlspecialchars($_GET['harga_min'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <input type="number" name="harga_max" class="form-control" placeholder="Harga max" 
                       value="<?= htmlspecialchars($_GET['harga_max'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" name="cari" class="btn btn-primary w-100">Cari</button>
            </div>
            <div class="col-md-2">
                <a href="index.php" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>

        <!-- Daftar Kamar -->
        <div class="row">
            <?php if ($kamar_list): ?>
                <?php foreach ($kamar_list as $kamar): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="uploads/kamar/<?= htmlspecialchars($kamar['foto']) ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($kamar['nama_kamar']) ?>" 
                                 style="height:200px; object-fit:cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($kamar['nama_kamar']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($kamar['deskripsi']) ?></p>
                                <p><strong>Rp <?= number_format($kamar['harga'], 0, ',', '.') ?> / bulan</strong></p>
                                <p><small>Ukuran: <?= htmlspecialchars($kamar['ukuran']) ?></small></p>
                                <p><small>Fasilitas: <?= htmlspecialchars($kamar['fasilitas_list'] ?? '-') ?></small></p>
                                <a href="<?= BASE_URL ?>detail.php?id=<?= $kamar['id_kamar'] ?>" class="btn btn-primary">Detail</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">Belum ada kamar tersedia.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>