<?php

header("Content-Type: application/json");

include('../connect/connect.php');

// Utwórz połączenie z bazą danych
$conn = new mysqli($servername, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection error: " . $conn->connect_error]));
}

// Pobierz dane z bazy
$sql = "SELECT *, TIMESTAMPDIFF(DAY, data_dodania, NOW()) as dni_temu FROM dane_klienci";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "imie" => $row['imie'],
            "nazwisko" => $row['nazwisko'],
            "email" => $row['email'],
            "telefon" => $row['telefon'],
            "data_dodania" => $row['data_dodania'],
            "wiadomosc" => $row['wiadomosc'],
            "highlighted" => (bool)$row['highlighted'],
            "dni_temu" => $row['dni_temu']
        ];
    }

    echo json_encode($data);
} else {
    echo json_encode(["message" => "Brak danych do wyświetlenia"]);
}

$conn->close();
