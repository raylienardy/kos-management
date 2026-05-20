<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../config/database.php';
$db = (new Database())->getConnection();

// Ringkasan
$kamar_tersedia = $db->query("SELECT COUNT(*) FROM kamar WHERE status_kamar='tersedia'")->fetchColumn();
$booking_pending = $db->query("SELECT COUNT(*) FROM booking WHERE status_booking='pending'")->fetchColumn();
$pemasukan_bulan_ini = $db->query("SELECT COALESCE(SUM(total_harga),0) FROM booking WHERE status_booking='diterima' AND MONTH(tanggal_booking)=MONTH(CURDATE()) AND YEAR(tanggal_booking)=YEAR(CURDATE())")->fetchColumn();
$total_user = $db->query("SELECT COUNT(*) FROM users WHERE role='penyewa'")->fetchColumn();


$page_title = 'Dashboard Admin - KosManagement';
include '../includes/header.php';
?>
<div class="content-wrapper container-fluid">
    <div class="row">
        <?php include '../includes/sidebar_admin.php'; ?>
        <main class="col-md-10 ms-sm-auto px-md-4">
            <h2 class="mt-3">Dashboard Admin</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?= $kamar_tersedia ?></h5>
                            <p class="card-text">Kamar Tersedia</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?= $booking_pending ?></h5>
                            <p class="card-text">Booking Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Rp <?= number_format($pemasukan_bulan_ini,0,',','.') ?></h5>
                            <p class="card-text">Pemasukan Bulan Ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?= $total_user ?></h5>
                            <p class="card-text">Penyewa Terdaftar</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>