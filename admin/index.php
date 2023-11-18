<?php
session_start();

// Ustawienia zabezpieczeń przed atakami CSRF
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Ustawienie nagłówków bezpieczeństwa
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");

header("Location: admin.php");
exit();
?>
