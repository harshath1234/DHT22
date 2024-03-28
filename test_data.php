<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Temperature and Humidity Readings</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 1500px;
        margin: 0 auto;
        background-color: #f1f1;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        height: 600px; /* Set a fixed height for the container */
        overflow: auto; /* Enable vertical scrolling if needed */
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    .graph-container {
        display: flex;
        justify-content: space-around;
        height: 80%; /* Adjusted height */
    }
    .graph {
        width: 45%;
        position: relative;
    }
    canvas {
        max-width: 100%;
        height: 100%;
    }
    .current-reading {
        text-align: center;
        margin-top: 10px;
        position: relative; /* Adjusted position */
    }
    .bulb {
        position: absolute;
        bottom: -110px; /* Adjusted position */
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid #000;
    }
    .warning-message {
        text-align: center;
        margin-top: 10px;
        position: absolute;
        bottom: -150px;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        color: red;
        font-weight: bold;
    }

    .non-warning-message {
        text-align: center;
        margin-top: 10px;
        position: absolute;
        bottom: -150px;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        color: green;
        font-weight: bold;
    }
    .green-bulb {
        background-color: green;
    }
    .red-bulb {
        background-color: red;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Temperature and Humidity Readings</h2>

    <div class="graph-container">
        <div class="graph">
            <canvas id="temperature-chart"></canvas>
            <div class="current-reading" id="current-temperature"></div>
            <div class="bulb green-bulb" id="temperature-bulb"></div>
            <div class="warning-message" id="temperature-warning"></div>
        </div>
        <div class="graph">
            <canvas id="humidity-chart"></canvas>
            <div class="current-reading" id="current-humidity"></div>
            <div class="bulb green-bulb" id="humidity-bulb"></div>
            <div class="warning-message" id="humidity-warning"></div>
        </div>
    </div>

    <?php
$hostname = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "sensor_db"; 

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) { 
    die("Connection failed: " . mysqli_connect_error()); 
} 

// Check if data is received
if (isset($_POST['temperature']) && isset($_POST['humidity'])) {
    // Retrieve POST data
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    
    // Get current datetime
    $datetime = date('Y-m-d H:i:s');
    
    // Prepare and execute SQL statement to insert data into the table
    $sql = "INSERT INTO dht11 (DT, Temperature, Humidity) VALUES ('$datetime', '$temperature', '$humidity')";
    if (mysqli_query($conn, $sql)) {
        echo "Data inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}


// Fetch temperature and humidity readings
$sql = "SELECT DT, Temperature, Humidity FROM dht11 ORDER BY id";
$result = mysqli_query($conn, $sql);

$timeLabels = [];
$temperatureData = [];
$humidityData = [];

while ($row = mysqli_fetch_assoc($result)) {
    $timeLabels[] = $row["DT"];
    $temperatureData[] = $row["Temperature"];
    $humidityData[] = $row["Humidity"];
}

mysqli_close($conn);
?>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/1.0.2/chartjs-plugin-annotation.min.js"></script>
    <script>
        var temperatureCtx = document.getElementById('temperature-chart').getContext('2d');
        var temperatureChart = new Chart(temperatureCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($timeLabels); ?>,
                datasets: [{
                    label: 'Temperature (°C)',
                    borderColor: 'rgb(255, 99, 132)',
                    data: <?php echo json_encode($temperatureData); ?>,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Temperature (°C)'
                        },
                        ticks: {
                            // Include a degree symbol after temperature values
                            callback: function(value, index, values) {
                                return value + '°C';
                            }
                        }
                    }
                },
                plugins: {
                    annotation: {
                        annotations: [{
                            type: 'line',
                            mode: 'horizontal',
                            scaleID: 'y',
                            value: 30,
                            borderColor: 'black',
                            borderWidth: 1,
                            label: {
                                enabled: true,
                                content: 'Threshold (30°C)',
                                position: 'right'
                            }
                        }]
                    }
                }
            }
        });

        var humidityCtx = document.getElementById('humidity-chart').getContext('2d');
        var humidityChart = new Chart(humidityCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($timeLabels); ?>,
                datasets: [{
                    label: 'Humidity (%)',
                    borderColor: 'rgb(54, 162, 235)',
                    data: <?php echo json_encode($humidityData); ?>,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Humidity (%)'
                        }
                    }
                },
                plugins: {
                    annotation: {
                        annotations: [{
                            type: 'line',
                            mode: 'horizontal',
                            scaleID: 'y',
                            value: 80,
                            borderColor: 'black',
                            borderWidth: 1,
                            label: {
                                enabled: true,
                                content: 'Threshold (80%)',
                                position: 'right'
                            }
                        }]
                    }
                }
            }
        });

        var currentTemperature = <?php echo end($temperatureData); ?>;
        var currentHumidity = <?php echo end($humidityData); ?>;
        document.getElementById('current-temperature').textContent = 'Current Temperature: ' + currentTemperature + '°C';
        document.getElementById('current-humidity').textContent = 'Current Humidity: ' + currentHumidity + '%';

        // Update bulb colors based on thresholds and display warning messages
        var temperatureBulb = document.getElementById('temperature-bulb');
        var humidityBulb = document.getElementById('humidity-bulb');
        var temperatureWarning = document.getElementById('temperature-warning');
        var humidityWarning = document.getElementById('humidity-warning');
        var temperatureThreshold = 30; // Example threshold for temperature
        var humidityThreshold = 80; // Example threshold for humidity

        if (currentTemperature > temperatureThreshold) {
            temperatureBulb.classList.remove('green-bulb');
            temperatureBulb.classList.add('red-bulb');
            temperatureWarning.textContent = 'Temperature exceeds threshold!';
        } else {
            temperatureWarning.textContent = ''; // Clear the warning if temperature is within threshold
        }

        if (currentHumidity > humidityThreshold) {
            humidityBulb.classList.remove('green-bulb');
            humidityBulb.classList.add('red-bulb');
            humidityWarning.textContent = 'Humidity exceeds threshold!';
        } else {
            humidityWarning.textContent = ''; // Clear the warning if humidity is within threshold
        }
    </script>
</div>
</body>
</html>




