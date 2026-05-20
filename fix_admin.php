<?php
require_once 'config/database.php';
$db = (new Database())->getConnection();
$new_hash = password_hash('admin123', PASSWORD_DEFAULT);
$db->exec("UPDATE users SET password = '$new_hash' WHERE email = 'admin@kos.com'");
echo "Password admin berhasil diperbarui!";