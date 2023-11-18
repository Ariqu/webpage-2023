<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://unpkg.com/pattern.css@1.0.0/dist/pattern.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <!-- Dodaj link do Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    
    

</head>
<body>

    <style>
        .highlight {
            background-color: #b7e1cd; /* Zielony kolor */
        }
        body {
            overflow-x: hidden; /* Ukryj poziomy pasek przewijania */
        }
        #sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            padding-top: 20px;
            padding-left: 10px;
            color: white;
        }
        #content {
            margin-left: 250px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        #content .card {
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    
<div id="content">

<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login']) && isset($_POST['password'])) {
        $login = $_POST['login'];
        $password = $_POST['password'];

        if ($login === 'admin' && $password === '1234') {
            $_SESSION['admin_logged_in'] = true;
        } else {
            echo '<div class="alert alert-danger">Invalid login or password.</div>';
        }
    }

    // Redirect to login.php
    header("Location: login.php");
    exit();
}
?>

<?php

include('connect/connect.php');

$conn = new mysqli($servername, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $id = $_POST['id'];

        if ($_POST['action'] == 'delete') {
            $delete_sql = "DELETE FROM dane_klienci WHERE id = $id";
            if ($conn->query($delete_sql) === TRUE) {
                echo "<div class='alert alert-success'>Record deleted successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error deleting record: " . $conn->error . "</div>";
            }
        } elseif ($_POST['action'] == 'highlight') {
            $highlight_sql = "UPDATE dane_klienci SET highlighted = NOT highlighted WHERE id = $id";
            if ($conn->query($highlight_sql) === TRUE) {
                echo "<div class='alert alert-success'>Record highlighted/unhighlighted successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error toggling highlight: " . $conn->error . "</div>";
            }
        }

        header("Location: admin.php");
        exit();
    }
}

$sql = "SELECT *, TIMESTAMPDIFF(DAY, data_dodania, NOW()) as dni_temu FROM dane_klienci";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    ?>


<div id="sidebar">
        <h2>System administracyjny</h2>

        <hr style="border-color: #bbb;">
        <div>
            <h5>Godzina:</h5>
            <p id="current-time"></p>
        </div>
        <div>
            <h5>Data:</h5>
            <p id="current-date"></p>
        </div>
        <div>
            <h5>Ilość rekordów:</h5>
            <p id="record-count"></p>
        </div>
        <!-- Dodaj obszar dla wykresu -->
        <div>
            <h5>System stworzony przez:</h5>
            <p class="lead"><i class="fas fa-user"></i> Jakub G.</p>
        </div>
        <div>
            <button onclick="refresh()" type="button" class="btn btn-primary">Odśwież</button>
            <button onclick="logout()" type="button" class="btn btn-primary">Wyloguj</button>
            <hr>
            <a href="download_data.php" class="btn btn-success">Pobierz dane (.txt)</a>
            <hr>
            <button onclick="stats()" type="button" class="btn btn-primary">
                Statystyki
                <span class="badge badge-info ml-2">BETA</span>
            </button>
        </div>
    </div>

    <div class="card mt-4">
    <div class="alert alert-primary d-flex align-items-center" role="alert">
            Po kliknięciu przycisku: <a href="#" class="alert-link">Podświetl</a> Data zostanie zresetowana.
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
    </div>

    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Admin</a></li>
        <li class="breadcrumb-item active" aria-current="page">System</li>
        </ol>
    </nav>


        <div class="card-body">
        <h1>System Administracyjny <span class="badge bg-secondary">ver.0.0.1</span></h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Email & Telefon</th>
                        <th>Data dodania</th>
                        <th>Treść</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        $rowClass = $row['highlighted'] ? 'highlight' : '';
                        echo "<tr class='$rowClass'>
                                <td>{$row['imie']}</td>
                                <td>{$row['nazwisko']}</td>
                                <td>{$row['email']}<br>{$row['telefon']}</td>
                                <td>(Dodano: {$row['dni_temu']} dni temu) | {$row['data_dodania']}</td>
                                <td>{$row['wiadomosc']}</td>
                                <td>
                                    <form method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <button type='submit' class='btn btn-danger' name='action' value='delete'>Delete</button>
                                    </form>
                                    <form method='post' style='display:inline;'>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <button type='submit' class='btn btn-success' name='action' value='highlight'>Unhighlight</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <a href="logout.php" class="btn btn-danger mt-3">Wyloguj</a>
        </div>
    </div>
    <?php
} else {
    echo '<h1 class="display-4">Błąd: <span class="badge bg-secondary">01</span></h1>';
    echo '<hr>';
    echo '<div class="alert alert-info" role="alert">';
    echo '<p class="lead">Błąd 01: Baza danych jest pusta, lub dane z niej zniknęły</p>';
    echo '<p>Jeżeli uważasz, że ta sytuacja nie powinna mieć miejsca, skontaktuj się z administratorem systemu</p>';
    echo '</div>';

    echo "<div class='alert alert-warning'>Brak danych do wyświetlenia</div>";
    echo "<br>";
    echo "<div class='alert alert-warning'>W bazie danych nie ma aktualnie żadnych danych.</div>";
    echo "<br>";
    echo '<div class="alert alert-info" role="alert"><a href="logout.php">Wyloguj się</a></div>';

}


?>


</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    // Function to update current time
    function updateCurrentTime() {
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();
        document.getElementById("current-time").innerText = hours + ":" + minutes + ":" + seconds;
    }

    // Function to update current date
    function updateCurrentDate() {
        var now = new Date();
        var year = now.getFullYear();
        var month = now.getMonth() + 1; // Months are zero-based
        var day = now.getDate();
        document.getElementById("current-date").innerText = year + "-" + month + "-" + day;
    }

    // Function to update record count
    function updateRecordCount() {
        // Use XMLHttpRequest to fetch record count from the server
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_record_count.php', true);

        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 400) {
                // Success!
                document.getElementById("record-count").innerText = xhr.responseText;
            } else {
                console.error('Error fetching record count');
            }
        };

        xhr.onerror = function () {
            console.error('Request failed');
        };

        xhr.send();
    }

    // Update information every second
    setInterval(function () {
        updateCurrentTime();
        updateCurrentDate();
        updateRecordCount();
    }, 1000);

    function refresh() {
        location.reload();
    }
    function logout() {
        location.href= 'logout.php';
    }
    function stats() {
        location.href = 'api/index_api.php';
    }
</script>



</body>

</html>
<?php

$conn->close();

?>