#include <WiFi.h>
#include <WiFiManager.h>
#include <HTTPClient.h>
#include <DHT.h>
#include <Preferences.h>

#define DHTPIN 26
#define DHTTYPE DHT22
#define RESET_BUTTON 0

DHT dht(DHTPIN, DHTTYPE);
WiFiManager wifiManager;
Preferences preferences;

char serverUrl[100] = "http://192.168.1.3/website/app/api/save_sensor_data.php";
char apiKey[50] = "smartgarden2025secret";
char sensorIdStr[10] = "1";
char sendIntervalStr[10] = "1000";

bool shouldSaveConfig = false;
unsigned long lastSend = 0;
unsigned long buttonPressStart = 0;
bool buttonPressed = false;

void saveConfigCallback() {
  Serial.println("Configuration should be saved");
  shouldSaveConfig = true;
}

void saveConfiguration() {
  preferences.begin("smartgarden", false);
  preferences.putString("serverUrl", serverUrl);
  preferences.putString("apiKey", apiKey);
  preferences.putString("sensorId", sensorIdStr);
  preferences.putString("interval", sendIntervalStr);
  preferences.end();
  Serial.println("Configuration saved to flash memory");
}

void loadConfiguration() {
  preferences.begin("smartgarden", true);
  
  String savedUrl = preferences.getString("serverUrl", "");
  String savedKey = preferences.getString("apiKey", "");
  String savedId = preferences.getString("sensorId", "");
  String savedInterval = preferences.getString("interval", "");
  
  if (savedUrl.length() > 0) strcpy(serverUrl, savedUrl.c_str());
  if (savedKey.length() > 0) strcpy(apiKey, savedKey.c_str());
  if (savedId.length() > 0) strcpy(sensorIdStr, savedId.c_str());
  if (savedInterval.length() > 0) strcpy(sendIntervalStr, savedInterval.c_str());
  
  preferences.end();
  
  Serial.println("Configuration loaded:");
  Serial.print("  Server URL: ");
  Serial.println(serverUrl);
  Serial.print("  Sensor ID: ");
  Serial.println(sensorIdStr);
  Serial.print("  Interval: ");
  Serial.println(sendIntervalStr);
}

void setup() {
  Serial.begin(115200);
  delay(1000);
  
  Serial.println("\n\n");
  Serial.println("╔═══════════════════════════════════════╗");
  Serial.println("║   SmartGarden ESP32 - WiFi Config    ║");
  Serial.println("╚═══════════════════════════════════════╝");
  
  pinMode(RESET_BUTTON, INPUT_PULLUP);
  dht.begin();
  
  // Load saved configuration
  loadConfiguration();
  
  WiFiManagerParameter custom_serverUrl("server", "Server URL", serverUrl, 100);
  WiFiManagerParameter custom_apiKey("apikey", "API Key", apiKey, 50);
  WiFiManagerParameter custom_sensorId("sensorid", "Sensor ID", sensorIdStr, 10);
  WiFiManagerParameter custom_interval("interval", "Send Interval (ms)", sendIntervalStr, 10);
  
  wifiManager.addParameter(&custom_serverUrl);
  wifiManager.addParameter(&custom_apiKey);
  wifiManager.addParameter(&custom_sensorId);
  wifiManager.addParameter(&custom_interval);
  
  wifiManager.setSaveConfigCallback(saveConfigCallback);
  
  wifiManager.setTitle("SmartGarden Setup");
  wifiManager.setDarkMode(false);
  wifiManager.setShowInfoUpdate(true);
  wifiManager.setConfigPortalTimeout(300);
  
  Serial.println("\n🔌 Connecting to WiFi...");
  Serial.println("────────────────────────────────────");
  
  if (!wifiManager.autoConnect("SmartGarden-Setup", "smartgarden123")) {
    Serial.println("❌ Failed to connect and timeout occurred");
    Serial.println("Restarting ESP32...");
    delay(3000);
    ESP.restart();
  }
  
  Serial.println("\n✅ WiFi Connected Successfully!");
  Serial.println("────────────────────────────────────");
  Serial.print("SSID: ");
  Serial.println(WiFi.SSID());
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
  Serial.print("Signal Strength (RSSI): ");
  Serial.print(WiFi.RSSI());
  Serial.println(" dBm");
  Serial.println("────────────────────────────────────\n");
  
  if (shouldSaveConfig) {
    strcpy(serverUrl, custom_serverUrl.getValue());
    strcpy(apiKey, custom_apiKey.getValue());
    strcpy(sensorIdStr, custom_sensorId.getValue());
    strcpy(sendIntervalStr, custom_interval.getValue());
    saveConfiguration();
  }
  
  Serial.println("📡 Starting sensor readings...");
  Serial.println("💡 Hold BOOT button for 3 seconds to reset WiFi\n");
}

void sendSensorData() {
  Serial.println("──────────────────────────────────");
  Serial.println("📊 Reading sensor...");
  
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();
  
  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("❌ ERROR: Failed to read from DHT sensor!");
    return;
  }
  
  Serial.print("🌡️  Temperature: ");
  Serial.print(temperature, 1);
  Serial.println(" °C");
  Serial.print("💧 Humidity: ");
  Serial.print(humidity, 1);
  Serial.println(" %");
  
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("⚠️  WiFi disconnected! Reconnecting...");
    WiFi.reconnect();
    delay(5000);
    return;
  }
  
  HTTPClient http;
  http.begin(serverUrl);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-Key", apiKey);
  http.setTimeout(10000);
  
  String jsonString = "{";
  jsonString += "\"capteurId\":" + String(sensorIdStr) + ",";
  jsonString += "\"temperature\":" + String(temperature, 2) + ",";
  jsonString += "\"humidite\":" + String(humidity, 2);
  jsonString += "}";
  
  Serial.print("📤 Sending to: ");
  Serial.println(serverUrl);
  Serial.print("📦 JSON: ");
  Serial.println(jsonString);
  
  int httpCode = http.POST(jsonString);
  
  if (httpCode > 0) {
    Serial.print("📥 HTTP Response: ");
    Serial.println(httpCode);
    
    if (httpCode == 200) {
      String response = http.getString();
      Serial.print("✅ SUCCESS: ");
      Serial.println(response);
    } else {
      Serial.print("⚠️  Warning: Server returned code ");
      Serial.println(httpCode);
    }
  } else {
    Serial.print("❌ ERROR: HTTP POST failed - ");
    Serial.println(http.errorToString(httpCode));
  }
  
  http.end();
  Serial.println("──────────────────────────────────\n");
}

void checkResetButton() {
  if (digitalRead(RESET_BUTTON) == LOW) {
    if (!buttonPressed) {
      buttonPressed = true;
      buttonPressStart = millis();
      Serial.println("🔘 BOOT button pressed...");
    }
    
    if (millis() - buttonPressStart > 3000) {
      Serial.println("\n🔄 RESETTING WiFi SETTINGS...");
      Serial.println("────────────────────────────────────");
      wifiManager.resetSettings();
      
      preferences.begin("smartgarden", false);
      preferences.clear();
      preferences.end();
      
      Serial.println("✅ WiFi settings cleared!");
      Serial.println("🔄 Restarting ESP32...\n");
      delay(1000);
      ESP.restart();
    }
  } else {
    buttonPressed = false;
  }
}

void loop() {
  checkResetButton();
  
  unsigned long currentMillis = millis();
  unsigned long sendInterval = String(sendIntervalStr).toInt();
  
  if (currentMillis - lastSend >= sendInterval) {
    lastSend = currentMillis;
    sendSensorData();
  }
  
  delay(100);
}
