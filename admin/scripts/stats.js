function drawDailyStatsChart(dates, counts) {
    var ctx = document.getElementById('daily-stats').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.map(date => new Date(date)),
            datasets: [{
                label: 'Dzienna ilość wpisów',
                data: counts,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: [{
                    type: 'time',
                    time: {
                        unit: 'day',
                        displayFormats: {
                            day: 'YYYY-MM-DD'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Data'
                    }
                }],
                y: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Ilość wpisów'
                    }
                }]
            }
        }
    });
}

function getDailyStats() {
    fetch('scripts/get_daily_stats.php')
        .then(response => response.json())
        .then(data => {
            var dates = data.map(entry => entry.date);
            var counts = data.map(entry => entry.count);

            drawDailyStatsChart(dates, counts);
        })
        .catch(error => {
            console.error('Error fetching daily stats', error);
        });
}

getDailyStats();

// Dodaj poniższy kod, aby przechwycić błędy w konsoli
window.onerror = function (message, source, lineno, colno, error) {
    console.error('Global error handler:', message, source, lineno, colno, error);
};