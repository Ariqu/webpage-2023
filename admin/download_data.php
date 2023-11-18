<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: admin.php");
    exit();
}

$database = 'dariadb';
$user = 'root';
$password = '';
$servername = 'localhost';

$conn = new mysqli($servername, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// Zapytanie do pobrania danych
$sql = "SELECT * FROM dane_klienci";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Utwórz tablicę do przechowywania danych
    $data = array();

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Utwórz zawartość pliku tekstowego
    $file_content = "";
    foreach ($data as $row) {
        $file_content .= implode("\t", $row) . "\n";
    }

    // Ustaw nagłówek dla pliku tekstowego
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="dane.txt"');

    // Wyślij zawartość pliku tekstowego do przeglądarki
    echo $file_content;
}

$conn->close();
?>
