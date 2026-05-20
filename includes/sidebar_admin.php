<?php
// includes/sidebar_admin.php
// Digunakan oleh semua halaman di folder admin/
// Variabel $current_page bisa di-set sebelum include, atau kita deteksi otomatis
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'kamar.php' ? 'active' : '' ?>" href="kamar.php">Kelola Kamar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'booking.php' ? 'active' : '' ?>" href="booking.php">Data Booking</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'verifikasi.php' ? 'active' : '' ?>" href="verifikasi.php">Verifikasi Pembayaran</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'users.php' ? 'active' : '' ?>" href="users.php">Kelola User</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'laporan.php' ? 'active' : '' ?>" href="laporan.php">Laporan</a>
            </li>
        </ul>
    </div>
</nav>