<?php
// Połączenie z bazą danych
$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'dariadb';

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pobranie danych statystycznych z bazy danych
$sql = "SELECT date, COUNT(*) as count FROM dane_klienci GROUP BY date";
$result = $conn->query($sql);

// Przygotowanie danych do odpowiedzi JSON
$response = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response[] = [
            'date' => $row['date'],
            'count' => $row['count']
        ];
    }
}

// Ustawienie nagłówka odpowiedzi JSON
header('Content-Type: application/json');

// Wysłanie danych jako odpowiedź JSON
echo json_encode($response);

// Zamknięcie połączenia z bazą danych
$conn->close();
?>
