<?php
session_start();
$_SESSION = [];               // kosongkan session
session_destroy();
header("Location: ../index.php");
exit;
?>