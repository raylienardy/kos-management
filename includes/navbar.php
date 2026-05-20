<?php
// Navbar ini mengasumsikan session sudah dimulai di file pemanggil.
if (!isset($_SESSION)) { session_start(); } // jaga-jaga
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>">KosManagement</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>">Beranda</a>
        </li>
        <?php if(isset($_SESSION['user'])): ?>
          <?php if($_SESSION['user']['role'] == 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/dashboard.php">Dashboard Admin</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>user/dashboard.php">Dashboard Saya</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>auth/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>auth/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>auth/register.php">Daftar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>