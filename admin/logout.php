<?php
session_start();
session_destroy();
header("Location: login.php"); // Przekieruj użytkownika na stronę logowania
exit();
?>
