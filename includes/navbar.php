<?php
// Navbar ini mengasumsikan session sudah dimulai di file pemanggil (index, dll).
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="/kos-management/index.php">KosManagement</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="/kos-management/index.php">Beranda</a>
        </li>
        <?php if(isset($_SESSION['user'])): ?>
          <?php if($_SESSION['user']['role'] == 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="/kos-management/admin/dashboard.php">Dashboard Admin</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="/kos-management/user/dashboard.php">Dashboard Saya</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="/kos-management/auth/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/kos-management/auth/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/kos-management/auth/register.php">Daftar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>