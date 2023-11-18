<?php
session_start();

// Dane do logowania
$validUsername = 'admin';
$validPassword = '1234';

// Sprawdź, czy kod bezpieczeństwa został wprowadzony poprawnie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['security_code']) && $_POST['security_code'] === $_SESSION['security_code']) {
        // Poprawny kod bezpieczeństwa
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        // Nieprawidłowy kod bezpieczeństwa
        $errorMessage = "Nieprawidłowy kod bezpieczeństwa. Spróbuj ponownie.";
    }
}

// Generuj nowy kod bezpieczeństwa i zapisz go w sesji
$_SESSION['security_code'] = generateSecurityCode();

// Funkcja do generowania kodu bezpieczeństwa
function generateSecurityCode($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <style>
        .container {
            max-width: 500px;
        }

        .card {
            margin-bottom: 20px;
        }

        .card-header {
            background-color: black;
            color: white;
        }

        .card-body {
            background-color: white;
        }

        .alert-danger {
            border-color: #dc3545;
            background-color: #f8d7da;
            color: #dc3545;
        }

        .form-control {
            border-radius: 0;
        }

        .btn-primary {
            border-radius: 0;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mt-5">
        <div class="card max-w-md mx-auto">
            <div class="card-header">
                <h2 class="card-title text-2xl font-semibold">Admin Login</h2>
                <p class="lead text-danger">Management system, for administration only</p>
            </div>


            <div class="card-body">
                <?php if (isset($errorMessage)) echo '<div class="alert alert-danger">' . $errorMessage . '</div>'; ?>
                <form method="post">
                    <div class="mb-4">
                        <label for="login" class="form-label">Login:</label>
                        <input type="text" id="login" name="login" class="form-control" placeholder="Wprowadź login" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Wprowadź hasło" required>
                    </div>
                    <!-- Nowy input do wprowadzania kodu bezpieczeństwa -->
                    <div class="mb-4">
                        <label for="security_code" class="form-label">Wprowadź kod bezpieczeństwa:</label>
                        <input type="text" id="security_code" name="security_code" class="form-control" required>
                        <span>KOD: <b style="user-select: none;"> <?php echo $_SESSION['security_code']; ?></b></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Log in</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
