#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>

#define DHTPIN 26
#define DHTTYPE DHT22

DHT dht(DHTPIN, DHTTYPE);

const char* ssid = "globalnet";
const char* password = "changeme";

const char* serverUrl = "http://192.168.1.3/website/app/api/save_sensor_data.php";
const char* apiKey = "smartgarden2025secret";
const int sensorId = 1;

const unsigned long sendInterval = 1000;
unsigned long lastSend = 0;

void setup() {
  Serial.begin(115200);
  Serial.println("\n\nSmartGarden ESP32 - HTTP Sensor");
  Serial.println("================================");
  
  dht.begin();
  
  setupWiFi();
  
  Serial.println("\nSetup complete! Starting sensor readings...\n");
}

void setupWiFi() {
  Serial.print("Connecting to WiFi: ");
  Serial.println(ssid);
  
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(500);
    Serial.print(".");
    attempts++;
  }
  
  Serial.println();
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("WiFi connected!");
    Serial.print("IP address: ");
    Serial.println(WiFi.localIP());
    Serial.print("Signal strength (RSSI): ");
    Serial.print(WiFi.RSSI());
    Serial.println(" dBm");
  } else {
    Serial.println("WiFi connection failed! Restarting in 5 seconds...");
    delay(5000);
    ESP.restart();
  }
}

void sendSensorData() {
  Serial.println("---------------------------");
  Serial.println("Reading sensor...");
  
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();
  
  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("ERROR: Failed to read from DHT sensor!");
    Serial.println("Check connections:");
    Serial.println("  - Data pin connected to GPIO 4?");
    Serial.println("  - Power (3.3V) and GND connected?");
    Serial.println("  - 10K pull-up resistor on data line?");
    return;
  }
  
  Serial.print("Temperature: ");
  Serial.print(temperature);
  Serial.println(" °C");
  Serial.print("Humidity: ");
  Serial.print(humidity);
  Serial.println(" %");
  
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("WiFi disconnected! Reconnecting...");
    setupWiFi();
    return;
  }
  
  HTTPClient http;
  http.begin(serverUrl);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-Key", apiKey);
  http.setTimeout(10000);
  
  String jsonString = "{";
  jsonString += "\"capteurId\":" + String(sensorId) + ",";
  jsonString += "\"temperature\":" + String(temperature, 2) + ",";
  jsonString += "\"humidite\":" + String(humidity, 2);
  jsonString += "}";
  
  Serial.print("Sending to server: ");
  Serial.println(serverUrl);
  Serial.print("JSON: ");
  Serial.println(jsonString);
  
  int httpCode = http.POST(jsonString);
  
  if (httpCode > 0) {
    Serial.print("HTTP Response code: ");
    Serial.println(httpCode);
    
    String response = http.getString();
    Serial.print("Server response: ");
    Serial.println(response);
    
    if (httpCode == 200) {
      Serial.println("SUCCESS: Data sent successfully! ✓");
    } else {
      Serial.print("WARNING: Server returned code ");
      Serial.println(httpCode);
    }
  } else {
    Serial.print("ERROR: HTTP POST failed: ");
    Serial.println(http.errorToString(httpCode));
    Serial.println("Possible issues:");
    Serial.println("  - Check server URL");
    Serial.println("  - Check if server is running (XAMPP started?)");
    Serial.println("  - Check firewall settings");
  }
  
  http.end();
  Serial.println("---------------------------\n");
}

void loop() {
  unsigned long now = millis();
  
  if (now - lastSend >= sendInterval) {
    lastSend = now;
    sendSensorData();
  }
  
  delay(100);
}
