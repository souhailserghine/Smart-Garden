<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Dashboard - SmartGarden</title>
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@1.9.2/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .sensor-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .sensor-value {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
        }
        .sensor-label {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .status-online {
            background: #28a745;
            color: white;
        }
        .status-offline {
            background: #dc3545;
            color: white;
        }
        .last-update {
            color: #999;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-white text-center">
                    <i class='bx bx-leaf'></i> Plant Sensor Dashboard
                </h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="sensor-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3><i class='bx bx-thermometer'></i> Temperature</h3>
                        <span class="status-badge" id="statusTemp">
                            <i class='bx bx-loader-circle bx-spin'></i> Loading...
                        </span>
                    </div>
                    <div class="sensor-value" id="temp">-- °C</div>
                    <div class="last-update" id="tempUpdate">Last update: --</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="sensor-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3><i class='bx bx-water'></i> Humidity</h3>
                        <span class="status-badge" id="statusHum">
                            <i class='bx bx-loader-circle bx-spin'></i> Loading...
                        </span>
                    </div>
                    <div class="sensor-value" id="hum">-- %</div>
                    <div class="last-update" id="humUpdate">Last update: --</div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="sensor-card">
                    <h4>Sensor Information</h4>
                    <p><strong>Sensor ID:</strong> <span id="sensorId">1</span></p>
                    <p><strong>Status:</strong> <span class="status-badge" id="overallStatus">Checking...</span></p>
                    <p><strong>Auto-refresh:</strong> Every 5 seconds</p>
                    <button class="btn btn-primary" onclick="fetchPlantData()">
                        <i class='bx bx-refresh'></i> Refresh Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const capteurId = 1;
        
        async function fetchPlantData() {
            try {
                const res = await fetch('../../api/get_sensor_data.php?capteurId=' + capteurId);
                const data = await res.json();
                
                if (data.temperature !== undefined && data.humidite !== undefined) {
                    document.getElementById('temp').innerText = data.temperature.toFixed(1) + ' °C';
                    document.getElementById('hum').innerText = data.humidite.toFixed(1) + ' %';
                    document.getElementById('tempUpdate').innerText = 'Last update: ' + data.timestamp;
                    document.getElementById('humUpdate').innerText = 'Last update: ' + data.timestamp;
                    document.getElementById('sensorId').innerText = data.capteurId;
                    
                    document.getElementById('statusTemp').innerHTML = '<i class="bx bx-check-circle"></i> Online';
                    document.getElementById('statusTemp').className = 'status-badge status-online';
                    document.getElementById('statusHum').innerHTML = '<i class="bx bx-check-circle"></i> Online';
                    document.getElementById('statusHum').className = 'status-badge status-online';
                    document.getElementById('overallStatus').innerHTML = '<i class="bx bx-check-circle"></i> Online';
                    document.getElementById('overallStatus').className = 'status-badge status-online';
                } else {
                    throw new Error('No data available');
                }
            } catch (err) {
                console.error('Failed to fetch data', err);
                document.getElementById('temp').innerText = '-- °C';
                document.getElementById('hum').innerText = '-- %';
                document.getElementById('statusTemp').innerHTML = '<i class="bx bx-x-circle"></i> Offline';
                document.getElementById('statusTemp').className = 'status-badge status-offline';
                document.getElementById('statusHum').innerHTML = '<i class="bx bx-x-circle"></i> Offline';
                document.getElementById('statusHum').className = 'status-badge status-offline';
                document.getElementById('overallStatus').innerHTML = '<i class="bx bx-x-circle"></i> Offline';
                document.getElementById('overallStatus').className = 'status-badge status-offline';
            }
        }

        setInterval(fetchPlantData, 1000);
        fetchPlantData();
    </script>
</body>
</html>
