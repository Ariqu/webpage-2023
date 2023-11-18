<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dane bazy danych
    $database = 'dariadb';
    $user = 'root';
    $password = '';
    $servername = 'localhost';

    // Połączenie z bazą danych
    $conn = new mysqli($servername, $user, $password, $database);

    if ($conn->connect_error) {
        die("Błąd połączenia z bazą danych: " . $conn->connect_error);
    }

    // Pobieranie danych z formularza
    $imie = isset($_POST['imie']) ? $_POST['imie'] : '';
    $nazwisko = isset($_POST['nazwisko']) ? $_POST['nazwisko'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $telefon = isset($_POST['telefon']) ? $_POST['telefon'] : '';
    $wiadomosc = isset($_POST['wiadomosc']) ? $_POST['wiadomosc'] : '';

    // Sprawdzenie, czy dane nie są puste
    if (!empty($imie) && !empty($nazwisko) && !empty($email) && !empty($telefon) && !empty($wiadomosc)) {
        // Rozpocznij transakcję
        $conn->begin_transaction();

        try {
            // SQL Query do wstawienia danych do tabeli dane_klienci
            $sql = $conn->prepare("INSERT INTO dane_klienci (imie, nazwisko, email, telefon, wiadomosc) VALUES (?, ?, ?, ?, ?)");
            $sql->bind_param("sssss", $imie, $nazwisko, $email, $telefon, $wiadomosc);

            if (!$sql->execute()) {
                throw new Exception("Błąd podczas zapisywania danych: " . $sql->error);
            }

            // Zaktualizuj dzienną liczbę użytkowników w tabeli statystyki
            $sql_update = $conn->query("UPDATE statystyki SET dzienna_ilosc_uzytkownikow = dzienna_ilosc_uzytkownikow + 1 WHERE data = CURRENT_DATE");

            if (!$sql_update) {
                throw new Exception("Błąd podczas aktualizacji danych statystycznych: " . $conn->error);
            }

            // Zakończ transakcję
            $conn->commit();

            echo "Dane zostały zapisane pomyślnie.";
            header('Location: thankyou.html');
            exit;
        } catch (Exception $e) {
            // W przypadku błędu, cofnij transakcję
            $conn->rollback();
            echo "Błąd: " . $e->getMessage();
        }

        $sql->close();
    } else {
        echo "Wszystkie pola formularza muszą być wypełnione.";
    }

    $conn->close();
}
?>

</body>
</html>
