<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';
$db = (new Database())->getConnection();

$msg = '';
$edit = null;

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $db->prepare("DELETE FROM kamar WHERE id_kamar=?")->execute([$id]);
    header("Location: kamar.php?msg=deleted");
    exit;
}

// Ambil data edit
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM kamar WHERE id_kamar=?");
    $stmt->execute([$id]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
    // ambil fasilitas terpilih
    $fas_selected = $db->prepare("SELECT id_fasilitas FROM kamar_fasilitas WHERE id_kamar=?");
    $fas_selected->execute([$id]);
    $selected_fas = $fas_selected->fetchAll(PDO::FETCH_COLUMN);
}

// Proses simpan (tambah/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kamar = isset($_POST['id_kamar']) ? (int)$_POST['id_kamar'] : 0;
    $nama = trim($_POST['nama_kamar']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = (float)$_POST['harga'];
    $ukuran = trim($_POST['ukuran']);
    $status = $_POST['status_kamar']; // tersedia, dipesan, terisi
    $fasilitas = isset($_POST['fasilitas']) ? $_POST['fasilitas'] : [];

    // Upload foto (jika ada)
    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $file = $_FILES['foto'];
        $allowed = ['image/jpeg', 'image/png'];
        $max = 2*1024*1024;
        if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], $allowed) && $file['size'] <= $max) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $foto = 'kamar_' . time() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], "../uploads/kamar/" . $foto);
        } else {
            $msg = '<div class="alert alert-danger">Upload foto gagal. Pastikan JPG/PNG max 2MB.</div>';
        }
    }

    if (empty($msg)) {
        try {
            $db->beginTransaction();
            if ($id_kamar > 0) {
                // Update
                if ($foto) {
                    $stmt = $db->prepare("UPDATE kamar SET nama_kamar=?, deskripsi=?, harga=?, ukuran=?, status_kamar=?, foto=? WHERE id_kamar=?");
                    $stmt->execute([$nama, $deskripsi, $harga, $ukuran, $status, $foto, $id_kamar]);
                } else {
                    $stmt = $db->prepare("UPDATE kamar SET nama_kamar=?, deskripsi=?, harga=?, ukuran=?, status_kamar=? WHERE id_kamar=?");
                    $stmt->execute([$nama, $deskripsi, $harga, $ukuran, $status, $id_kamar]);
                }
                // Hapus fasilitas lama
                $db->prepare("DELETE FROM kamar_fasilitas WHERE id_kamar=?")->execute([$id_kamar]);
            } else {
                // Insert
                $foto = $foto ?: 'default-kamar.jpg';
                $stmt = $db->prepare("INSERT INTO kamar (nama_kamar, deskripsi, harga, ukuran, status_kamar, foto) VALUES (?,?,?,?,?,?)");
                $stmt->execute([$nama, $deskripsi, $harga, $ukuran, $status, $foto]);
                $id_kamar = $db->lastInsertId();
            }
            // Insert fasilitas
            $stmt_fas = $db->prepare("INSERT INTO kamar_fasilitas (id_kamar, id_fasilitas) VALUES (?,?)");
            foreach ($fasilitas as $id_fas) {
                $stmt_fas->execute([$id_kamar, $id_fas]);
            }
            $db->commit();
            $msg = '<div class="alert alert-success">Data kamar berhasil disimpan.</div>';
            $edit = null; // reset form
        } catch (Exception $e) {
            $db->rollBack();
            $msg = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Ambil semua kamar + fasilitas (group_concat)
$kamar_list = $db->query("SELECT k.*, GROUP_CONCAT(f.nama_fasilitas SEPARATOR ', ') AS fasilitas 
                         FROM kamar k LEFT JOIN kamar_fasilitas kf ON k.id_kamar=kf.id_kamar 
                         LEFT JOIN fasilitas f ON kf.id_fasilitas=f.id_fasilitas 
                         GROUP BY k.id_kamar ORDER BY k.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$fasilitas_all = $db->query("SELECT * FROM fasilitas")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kamar - Admin</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="content-wrapper container-fluid">
    <div class="row">
        <?php include '../includes/sidebar_admin.php'; ?>
        <main class="col-md-10 ms-sm-auto px-md-4">
            <h2 class="mt-3">Kelola Kamar</h2>
            <?= $msg ?>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalKamar">Tambah Kamar Baru</button>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Foto</th><th>Nama</th><th>Harga</th><th>Status</th><th>Fasilitas</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($kamar_list as $k): ?>
                    <tr>
                        <td><img src="../uploads/kamar/<?= htmlspecialchars($k['foto']) ?>" width="80"></td>
                        <td><?= htmlspecialchars($k['nama_kamar']) ?></td>
                        <td>Rp <?= number_format($k['harga'],0,',','.') ?></td>
                        <td><span class="badge bg-<?= $k['status_kamar']=='tersedia'?'success':($k['status_kamar']=='dipesan'?'warning':'danger') ?>"><?= $k['status_kamar'] ?></span></td>
                        <td><?= htmlspecialchars($k['fasilitas']??'-') ?></td>
                        <td>
                            <a href="?edit=<?= $k['id_kamar'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?hapus=<?= $k['id_kamar'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</div>

<!-- Modal Form Kamar -->
<div class="modal fade" id="modalKamar" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?= $edit ? 'Edit Kamar' : 'Tambah Kamar' ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id_kamar" value="<?= $edit['id_kamar'] ?? '' ?>">
            <div class="mb-3">
                <label>Nama Kamar</label>
                <input type="text" name="nama_kamar" class="form-control" value="<?= htmlspecialchars($edit['nama_kamar']??'') ?>" required>
            </div>
            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control"><?= htmlspecialchars($edit['deskripsi']??'') ?></textarea>
            </div>
            <div class="mb-3">
                <label>Harga</label>
                <input type="number" name="harga" class="form-control" value="<?= $edit['harga']??'' ?>" required>
            </div>
            <div class="mb-3">
                <label>Ukuran</label>
                <input type="text" name="ukuran" class="form-control" value="<?= htmlspecialchars($edit['ukuran']??'') ?>">
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status_kamar" class="form-control">
                    <option value="tersedia" <?= (isset($edit) && $edit['status_kamar']=='tersedia')?'selected':'' ?>>Tersedia</option>
                    <option value="dipesan" <?= (isset($edit) && $edit['status_kamar']=='dipesan')?'selected':'' ?>>Dipesan</option>
                    <option value="terisi" <?= (isset($edit) && $edit['status_kamar']=='terisi')?'selected':'' ?>>Terisi</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Fasilitas</label>
                <div class="row">
                    <?php foreach ($fasilitas_all as $f): 
                        $checked = (isset($selected_fas) && in_array($f['id_fasilitas'], $selected_fas)) ? 'checked' : '';
                    ?>
                    <div class="col-6"><label><input type="checkbox" name="fasilitas[]" value="<?= $f['id_fasilitas'] ?>" <?= $checked ?>> <?= $f['nama_fasilitas'] ?></label></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="mb-3">
                <label>Foto (JPG/PNG, max 2MB)</label>
                <input type="file" name="foto" class="form-control">
                <?php if ($edit && $edit['foto']): ?>
                    <small>Foto saat ini: <img src="../uploads/kamar/<?= $edit['foto'] ?>" width="60"></small>
                <?php endif; ?>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<?php if ($edit): ?>
<script>
    // Jika edit, buka modal otomatis
    var modal = new bootstrap.Modal(document.getElementById('modalKamar'));
    modal.show();
</script>
<?php endif; ?>
</body>
</html>