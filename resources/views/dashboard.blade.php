
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#153653">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Sensor Monitor">
    <link rel="icon" href="/icons/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <title>ESP32 Sensor Monitoring</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: #f2f5fa;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #153653;
        }

        .sensor-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(0,0,0,.06);
            height: 100%;
        }

        .sensor-value {
            font-size: 2rem;
            font-weight: 700;
            color: #153653;
        }

        .sensor-label {
            color: #6c757d;
            font-size: .9rem;
        }

        .chart-card {
            border: 0;
            border-radius: 16px;
        }

        .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #adb5bd;
            margin-right: 5px;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-dark py-3">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span class="navbar-brand fw-bold mb-0">
            AUTOMATED POULTRY MONITORING SYSTEM
        </span>

        <div class="d-flex align-items-center gap-2">
            <span class="text-white small d-none d-md-inline">
                IoT Dashboard
            </span>

            <button
                id="installButton"
                class="btn btn-light btn-sm fw-semibold"
                type="button"
                aria-label="Install Sensor Monitor app">
                <span aria-hidden="true">⬇</span> Install App
            </button>
        </div>
    </div>
</nav>

<!-- aa -->


<main class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Sensor Dashboard</h3>
            <div class="text-secondary">
                Monitor your environment in real time
            </div>
        </div>

        <div class="text-end small">
            <div>
                <span class="status-dot" id="statusDot"></span>
                <span id="connectionStatus">Connecting...</span>
            </div>
            <div class="text-secondary mt-1" id="lastUpdated">
                Waiting for readings...
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card sensor-card">
                <div class="card-body">
                    <div class="sensor-label">HC-SR04</div>
                    <h6 class="mt-2">Water Level</h6>
                    <div class="sensor-value">
                        <span id="distance">--</span>
                        <small class="fs-6">cm</small>
                    </div>
                    <div class="text-secondary small mt-2">
                        Water measurement
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card sensor-card">
                <div class="card-body">
                    <div class="sensor-label">DHT11</div>
                    <h6 class="mt-2">Temperature</h6>
                    <div class="sensor-value">
                        <span id="temperature">--</span>
                        <small class="fs-6">°C</small>
                    </div>
                    <div class="text-secondary small mt-2">
                        Ambient temperature
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card sensor-card">
                <div class="card-body">
                    <div class="sensor-label">DHT11</div>
                    <h6 class="mt-2">Humidity</h6>
                    <div class="sensor-value">
                        <span id="humidity">--</span>
                        <small class="fs-6">%</small>
                    </div>
                    <div class="text-secondary small mt-2">
                        Relative humidity
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card sensor-card">
                <div class="card-body">
                    <div class="sensor-label">MQ-135</div>
                    <h6 class="mt-2">Raw Gas Signal</h6>
                    <div class="sensor-value">
                        <span id="mq135">--</span>
                    </div>
                    <div class="text-secondary small mt-2">
                        ESP32 ADC reading (0–4095)
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card sensor-card">
                <div class="card-body">
                    <h6>MQ-135 Relative Signal Indicator</h6>
                    <div class="sensor-value">
                        <span id="mqPercent">--</span>
                        <small class="fs-6">%</small>
                    </div>
                    <div class="progress mt-3">
                        <div
                            id="mqBar"
                            class="progress-bar bg-info"
                            style="width:0%"
                        ></div>
                    </div>
                    <p class="text-secondary small mt-2 mb-0">
                        Normalized ADC signal, not ammonia concentration
                        or a calibrated air-quality score.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card sensor-card">
                <div class="card-body">
                    <h6>HC-SR04 Measurement</h6>
                    <p class="text-secondary small">
                        Distance reported by the ultrasonic sensor.
                    </p>
                    <div class="sensor-value">
                        <span id="distanceStatus">Waiting</span>
                    </div>
                    <p class="text-secondary small mt-2 mb-0">
                        Confirm the configured distance range matches
                        your application.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card chart-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold">Temperature History</h5>
            <div style="height:300px">
                <canvas id="temperatureChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card chart-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold">Humidity History</h5>
            <div style="height:300px">
                <canvas id="humidityChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card chart-card shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold">Recent Sensor Readings</h5>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Date and Time</th>
                            <th>Water Level (cm)</th>
                            <th>Temperature (°C)</th>
                            <th>Humidity (%)</th>
                            <th>MQ-135 ADC</th>
                        </tr>
                    </thead>
                    <tbody id="historyTable">
                        <tr>
                            <td colspan="5" class="text-center">
                                Waiting for sensor readings...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<script>
    const numberFormat = (value, digits = 1) => {
        if (value === null || value === undefined || value === '') {
            return '--';
        }

        const number = Number(value);
        return Number.isFinite(number)
            ? number.toFixed(digits)
            : '--';
    };

    function setConnection(connected, message) {
        document.getElementById('connectionStatus').textContent = message;

        document.getElementById('statusDot').style.background =
            connected ? '#198754' : '#dc3545';
    }

    function makeChart(canvasId, label, color) {
        return new Chart(document.getElementById(canvasId), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label,
                    data: [],
                    borderColor: color,
                    backgroundColor: color,
                    tension: 0.3,
                    pointRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    }

    const temperatureChart = makeChart(
        'temperatureChart', 'Temperature (°C)', '#dc3545'
    );

    const humidityChart = makeChart(
        'humidityChart', 'Humidity (%)', '#0d6efd'
    );

    function formatTime(value) {
        if (!value) return '--';

        const date = new Date(value.replace(' ', 'T'));

        return Number.isNaN(date.getTime())
            ? value
            : date.toLocaleString();
    }

    async function loadLatest() {
        const response = await fetch('/readings/latest', {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store'
        });

        if (!response.ok) {
            throw new Error('Unable to load latest readings.');
        }

        const result = await response.json();
        const data = result.data;

        if (!data) {
            setConnection(false, 'No readings yet');
            return;
        }

        document.getElementById('distance').textContent =
            numberFormat(data.distance_cm);

        document.getElementById('temperature').textContent =
            numberFormat(data.temperature_c);

        document.getElementById('humidity').textContent =
            numberFormat(data.humidity_percent);

        document.getElementById('mq135').textContent =
            data.mq135_raw ?? '--';

        document.getElementById('mqPercent').textContent =
            numberFormat(data.mq135_signal_percent);

        document.getElementById('mqBar').style.width =
            `${Math.min(100, Math.max(0, Number(data.mq135_signal_percent) || 0))}%`;

        document.getElementById('distanceStatus').textContent =
            data.distance_cm == null
                ? 'No measurement'
                : `${numberFormat(data.distance_cm)} cm`;

        document.getElementById('lastUpdated').textContent =
            'Last reading: ' + formatTime(data.recorded_at);

        setConnection(true, 'Server connected');
    }

    async function loadHistory() {
        const response = await fetch('/readings/history', {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store'
        });

        if (!response.ok) {
            throw new Error('Unable to load history.');
        }

        const result = await response.json();
        const rows = result.data || [];

        const labels = rows.map(row => {
            const date = new Date(
                String(row.recorded_at).replace(' ', 'T')
            );

            return Number.isNaN(date.getTime())
                ? row.recorded_at
                : date.toLocaleTimeString();
        });

        temperatureChart.data.labels = labels;
        temperatureChart.data.datasets[0].data =
            rows.map(row => row.temperature_c);

        humidityChart.data.labels = labels;
        humidityChart.data.datasets[0].data =
            rows.map(row => row.humidity_percent);

        temperatureChart.update();
        humidityChart.update();

        const table = document.getElementById('historyTable');

        if (rows.length === 0) {
            table.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">
                        No readings recorded yet.
                    </td>
                </tr>`;
            return;
        }

        table.replaceChildren();

        rows.slice().reverse().forEach(row => {
            const tr = document.createElement('tr');

            [
                formatTime(row.recorded_at),
                numberFormat(row.distance_cm),
                numberFormat(row.temperature_c),
                numberFormat(row.humidity_percent),
                row.mq135_raw ?? '--'
            ].forEach(value => {
                const td = document.createElement('td');
                td.textContent = value;
                tr.appendChild(td);
            });

            table.appendChild(tr);
        });
    }

    async function refreshDashboard() {
        try {
            await Promise.all([loadLatest(), loadHistory()]);
        } catch (error) {
            setConnection(false, 'Connection error');
            console.error(error);
        }
    }

    refreshDashboard();
    setInterval(refreshDashboard, 3000);

    
if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            await navigator.serviceWorker.register('/sw.js');
            console.log('Sensor Monitor PWA service worker registered.');
        } catch (error) {
            console.error('Service worker registration failed:', error);
        }
    });
}

let deferredInstallPrompt = null;

const installButton = document.getElementById('installButton');

// Keep the button visible. Some browsers do not fire
// beforeinstallprompt, even when the PWA can be installed manually.
window.addEventListener('beforeinstallprompt', event => {
    event.preventDefault();
    deferredInstallPrompt = event;
});

installButton.addEventListener('click', async () => {
    if (deferredInstallPrompt) {
        deferredInstallPrompt.prompt();

        try {
            await deferredInstallPrompt.userChoice;
        } catch (error) {
            console.error('Install prompt was dismissed or failed:', error);
        }

        deferredInstallPrompt = null;
        return;
    }

    // Browser fallback: explain how to install manually.
    alert(
        'To install Sensor Monitor, open your browser menu and choose ' +
        '"Install app" or "Add to Home screen". ' +
        'If this option is missing, check that the site is using HTTPS ' +
        '(or localhost), the manifest and icons load correctly, and the ' +
        'service worker is active.'
    );
});

window.addEventListener('appinstalled', () => {
    installButton.textContent = 'App Installed';
    installButton.disabled = true;
    deferredInstallPrompt = null;
    console.log('Sensor Monitor PWA installed.');
});
</script>

</body>

</html>

