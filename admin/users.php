<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';
$db = (new Database())->getConnection();

// Hapus user
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $db->prepare("DELETE FROM users WHERE id_user=? AND role='penyewa'")->execute([$id]);
    header("Location: users.php");
    exit;
}

$users = $db->query("SELECT id_user, nama, email, no_hp, created_at FROM users WHERE role='penyewa' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
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
                    <li class="nav-item"><a class="nav-link active" href="users.php">Kelola User</a></li>
                    <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
                </ul>
            </div>
        </nav>
        <main class="col-md-10 ms-sm-auto px-md-4">
            <h2 class="mt-3">Daftar Penyewa</h2>
            <table class="table table-bordered">
                <thead><tr><th>Nama</th><th>Email</th><th>No HP</th><th>Tanggal Daftar</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nama']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['no_hp']) ?></td>
                        <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                        <td><a href="?hapus=<?= $u['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')">Hapus</a></td>
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