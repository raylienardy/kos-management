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

// Ambil data user saat ini
$stmt = $db->prepare("SELECT nama, email, no_hp, alamat FROM users WHERE id_user = :id");
$stmt->execute([':id' => $id_user]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama']);
    $no_hp  = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);

    if (empty($nama) || empty($no_hp) || empty($alamat)) {
        $error = 'Semua field wajib diisi.';
    } else {
        $stmt = $db->prepare("UPDATE users SET nama = :nama, no_hp = :no_hp, alamat = :alamat WHERE id_user = :id");
        $stmt->execute([
            ':nama'   => $nama,
            ':no_hp'  => $no_hp,
            ':alamat' => $alamat,
            ':id'     => $id_user
        ]);
        $_SESSION['user']['nama'] = $nama; // update session
        $success = "Profil berhasil diperbarui.";
        // Refresh data
        $userData = ['nama' => $nama, 'email' => $userData['email'], 'no_hp' => $no_hp, 'alamat' => $alamat];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - KosManagement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4" style="max-width: 500px;">
        <h3>Edit Profil</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($userData['nama']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" disabled>
                <small class="text-muted">Email tidak dapat diubah.</small>
            </div>
            <div class="mb-3">
                <label>No HP</label>
                <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($userData['no_hp']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="2" required><?= htmlspecialchars($userData['alamat']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>