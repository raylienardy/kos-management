<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="col-md-2 d-none d-md-block sidebar-dark bg-dark text-white" style="min-height: 100vh;">
    <div class="d-flex flex-column flex-shrink-0 p-3">
        <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <i class="bi bi-building me-2 fs-4"></i>
            <span class="fs-5 fw-bold">KosAdmin</span>
        </a>
        <hr class="text-secondary">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link text-white <?= $current_page == 'dashboard.php' ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="kamar.php" class="nav-link text-white <?= $current_page == 'kamar.php' ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-door-open me-2"></i> Kelola Kamar
                </a>
            </li>
            <li>
                <a href="booking.php" class="nav-link text-white <?= $current_page == 'booking.php' ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-journal-text me-2"></i> Data Booking
                </a>
            </li>
            <li>
                <a href="verifikasi.php" class="nav-link text-white <?= $current_page == 'verifikasi.php' ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-check-circle me-2"></i> Verifikasi
                </a>
            </li>
            <li>
                <a href="users.php" class="nav-link text-white <?= $current_page == 'users.php' ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-people me-2"></i> Kelola User
                </a>
            </li>
            <li>
                <a href="laporan.php" class="nav-link text-white <?= $current_page == 'laporan.php' ? 'active bg-primary' : '' ?>">
                    <i class="bi bi-bar-chart me-2"></i> Laporan
                </a>
            </li>
        </ul>
        <hr class="text-secondary">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-2 fs-5"></i>
                <strong><?= htmlspecialchars($_SESSION['user']['nama'] ?? 'Admin') ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                <li><a class="dropdown-item" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</div>