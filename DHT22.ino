#include <ESP8266WiFi.h>
#include <DHT.h>

#define DHT_SENSOR_PIN  D7 // The ESP8266 pin D7 connected to DHT22 sensor
#define DHT_SENSOR_TYPE DHT22

DHT dht22(DHT_SENSOR_PIN, DHT_SENSOR_TYPE);

const char* ssid = "uday4G"; // Enter your WiFi SSID
const char* password = "Pgrukh@1978"; // Enter your WiFi password

float temperatureCelsius = 0.0f;
float humidityPercentage = 0.0f;

const char* serverAddress = "192.168.29.35"; // Correct IP address of the server // IP address of the server
const int serverPort = 80; // Port of the server
const char* endpoint = "https://192.168.29.35/dht11_project/test_data.php"; // Endpoint to send data

void setup() {
  Serial.begin(115200);
  dht22.begin();
  connectWiFi();
}

void loop() {
  if(WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }

  loadDHT22Data();
  String postData = "temperature=" + String(temperatureCelsius) + "&humidity=" + String(humidityPercentage) ;


  if (sendData(postData)) {
    Serial.println("Data sent successfully");
  } else {
    Serial.println("Failed to send data");
  }

  Serial.println("--------------------------------------------------");
  delay(5000); // Delay before next loop iteration
}

void loadDHT22Data() {
  temperatureCelsius = dht22.readTemperature(); // Celsius
  humidityPercentage = dht22.readHumidity();

  if (isnan(temperatureCelsius) || isnan(humidityPercentage)) {
    Serial.println("Failed to read from DHT sensor!");
    temperatureCelsius = 0.0f;
    humidityPercentage = 0.0f;
  }

  Serial.printf("Temperature: %.2f °C\n", temperatureCelsius);
  Serial.printf("Humidity: %.2f %%\n", humidityPercentage);
}

bool sendData(String data) {
  WiFiClient client;

  if (!client.connect(serverAddress, serverPort)) {
    Serial.println("Connection to server failed");
    return false;
  }

  Serial.println("Connected to server");

  client.print(String("POST ") + endpoint + " HTTP/1.1\r\n");
  client.print(String("Host: ") + serverAddress + "\r\n");
  client.println("Content-Type: application/x-www-form-urlencoded");
  client.print("Content-Length: ");
  client.println(data.length());
  client.println();
  client.print(data);

  delay(1000); // Allow time for server response

  if (client.connected()) {
    client.stop();
    return true;
  } else {
    return false;
  }
}

void connectWiFi() {
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.mode(WIFI_STA);

  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.print("Connected to: ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}


