<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wyszukiwarka: Bazy Danych</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="container mt-5">

    <div>
        <h1 class="mb-4">Wyszukiwarka: Bazy Danych</h1>
        <div class='alert alert-danger'>Strona w trakcie tworzenia</div>
        <!-- Wyszukiwarka -->
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Wyszukaj informacje" id="searchInput">
            <button class="btn btn-outline-secondary" type="button" onclick="searchData()">Szukaj</button>
        </div>

        <!-- Wyniki wyszukiwania -->
        <div id="searchResults" class="card mt-4" style="display: none;">
            <div class="card-body">
                <h2 class="card-title">Wyniki wyszukiwania</h2>
                <ul id="searchResultsList" class="list-group"></ul>
            </div>
        </div>

        <!-- Alert brak wyników -->
        <div id="noResultsAlert" class="alert alert-warning mt-4" style="display: none;">
            Przepraszamy, w bazie danych nie ma takich informacji.
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h2 class="card-title">Podsumowanie</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Statystyka</th>
                            <th>Wartość</th>
                        </tr>
                    </thead>
                    <tbody id="statisticsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
    // Kod dla strony wyszukiwania danych
    // (możesz umieścić tutaj kod, który chcesz wykonać po załadowaniu strony)
});

function searchData() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const searchResultsList = document.getElementById('searchResultsList');
    const statisticsTableBody = document.getElementById('statisticsTableBody');
    const noResultsAlert = document.getElementById('noResultsAlert');

    // Pobierz dane z API
    fetch("http://localhost/daria/admin/api/api.php")
        .then(response => response.json())
        .then(data => {
            // Przeszukaj dane i wyświetl wyniki w searchResultsList
            const results = data.filter(item =>
                item.nazwisko.toLowerCase().includes(searchInput) ||
                item.imie.toLowerCase().includes(searchInput) ||
                item.email.toLowerCase().includes(searchInput) ||
                item.telefon.toLowerCase().includes(searchInput)
            );

            // Wyświetl wyniki lub alert braku wyników
            if (results.length > 0) {
                displaySearchResults(results, searchResultsList);
                noResultsAlert.style.display = 'none';
            } else {
                searchResultsList.innerHTML = ''; // Wyczyść wyniki, jeśli były wcześniej
                noResultsAlert.style.display = 'block';
            }

            // Przykładowe dodanie wyników do tabeli statystyk
            statisticsTableBody.innerHTML = `
                <tr>
                    <td>Liczba znalezionych informacji</td>
                    <td>${results.length}</td>
                </tr>
            `;

            // Wyświetl wyniki
            document.getElementById('searchResults').style.display = 'block';
        })
        .catch(error => console.error("Błąd pobierania danych z API:", error));
}

function displaySearchResults(results, listElement) {
    listElement.innerHTML = '';
    results.forEach(result => {
        const listItem = document.createElement('li');
        // Przykładowo: nazwa, imie, nazwisko - dostosuj do rzeczywistych pól w danych JSON
        listItem.textContent = `Nazwa: ${result.nazwa}, Imię: ${result.imie}, Nazwisko: ${result.nazwisko}`;
        listItem.className = 'list-group-item';
        listElement.appendChild(listItem);
    });
}

    </script>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

</body>

</html>
