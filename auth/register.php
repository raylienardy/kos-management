<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $email    = trim($_POST['email']);
    $no_hp    = trim($_POST['no_hp']);
    $alamat   = trim($_POST['alamat']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Validasi sederhana
    if (empty($nama) || empty($email) || empty($no_hp) || empty($alamat) || empty($password)) {
        $error = 'Semua field wajib diisi.';
    } elseif ($password !== $confirm) {
        $error = 'Password dan konfirmasi tidak cocok.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        // Cek email unik
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare("SELECT id_user FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            // Insert user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (nama, email, password, no_hp, alamat) VALUES (:nama, :email, :password, :no_hp, :alamat)");
            $stmt->execute([
                ':nama'     => $nama,
                ':email'    => $email,
                ':password' => $hashed,
                ':no_hp'    => $no_hp,
                ':alamat'   => $alamat
            ]);
            $success = 'Registrasi berhasil. Silakan <a href="login.php">login</a>.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - KosManagement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5" style="max-width: 500px;">
        <h3 class="mb-4">Registrasi Akun</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nomor HP</label>
                <input type="text" name="no_hp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="2" required></textarea>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>
        <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>