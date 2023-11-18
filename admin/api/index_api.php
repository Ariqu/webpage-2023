<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    // Przekieruj na stronę logowania, jeśli użytkownik nie jest zalogowany
    header("Location: ../login.php");
    exit();
}

include('../connect/connect.php');

$conn = new mysqli($servername, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Interface</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap Table CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.18.1/bootstrap-table.min.css">
</head>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.html">ADMIN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../admin.php">Powrót > </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../index.html">Strona Główna</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Wyloguj</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<body class="container mt-5">

    <div>
        <h1 class="mb-4">Statystyki</h1>
        <div class='alert alert-danger'>Strona w trakcie tworzenia</div>
        <div class="card">
            <div class="card-body">
                <canvas id="myChart" width="400" height="200"></canvas>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body">
                
                <table class="table">
                    </thead>
                    <tbody id="statisticsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Bootstrap Table JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.18.1/bootstrap-table.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const apiUrl = "http://localhost/daria/admin/api/api.php";

            // Pobierz dane z API
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    // Przygotuj dane do statystyk
                    const currentDate = new Date();
                    const startDateWeek = new Date(currentDate);
                    startDateWeek.setDate(currentDate.getDate() - 7); // Dzisiaj - 7 dni
                    const startDateYesterday = new Date(currentDate);
                    startDateYesterday.setDate(currentDate.getDate() - 1); // Dzisiaj - 1 dzień

                    const dataWeek = data.filter(item => new Date(item.data_dodania) >= startDateWeek);
                    const dataYesterday = data.filter(item => new Date(item.data_dodania) >= startDateYesterday && new Date(item.data_dodania) < currentDate);
                    const dataToday = data.filter(item => new Date(item.data_dodania) >= currentDate);

                    const addedWeek = dataWeek.length;
                    const addedYesterday = dataYesterday.length;
                    const addedToday = dataToday.length;

                    const average = calculateAverage(data);
                    const median = calculateMedian(data);
                    const mode = calculateMode(data);

                    // Utwórz statystyki
                    const ctx = document.getElementById('myChart').getContext('2d');
                    const myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['W tym tygodniu', 'Wczoraj', 'Dziś'],
                            datasets: [{
                                label: 'Liczba dodanych informacji',
                                data: [addedWeek, addedYesterday, addedToday],
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 99, 132, 0.7)'
                                ],
                                borderWidth: 1
                            }]
                        }
                    });

                    
                })
                .catch(error => console.error("Błąd pobierania danych z API:", error));
        });

        function calculateAverage(data) {
            const sum = data.reduce((acc, item) => acc + 1, 0);
            return sum / data.length || 0;
        }

        function calculateMedian(data) {
            const sortedData = data.map(item => new Date(item.data_dodania).getTime()).sort((a, b) => a - b);
            const middle = Math.floor(sortedData.length / 2);
            return (sortedData.length % 2 === 0) ?
                (sortedData[middle - 1] + sortedData[middle]) / 2 :
                sortedData[middle];
        }

        function calculateMode(data) {
            const counts = {};
            let maxCount = 0;
            let mode = null;

            data.forEach(item => {
                const key = new Date(item.data_dodania).toDateString();
                counts[key] = (counts[key] || 0) + 1;

                if (counts[key] > maxCount) {
                    maxCount = counts[key];
                    mode = key;
                }
            });

            return mode;
        }
    </script>

</body>

</html>

<?php
$conn->close();
?>