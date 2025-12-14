# ✅ IoT Database Integration - COMPLETED

## What Was Changed

### 1. Updated `plantController.php`
**Location:** `app/controller/plantController.php`

**Changes:**
- ✅ Removed JSON file storage system
- ✅ Added database connection via `config::getConnexion()`
- ✅ Updated `saveSensorData()` to INSERT data into `sensor_data` table
- ✅ Updated `getLatestData()` to SELECT latest reading from database
- ✅ Updated `getAllSensors()` to get latest reading for all sensors

### 2. Created New API Endpoint
**File:** `app/api/get_sensor_history.php`

**Purpose:** Get historical sensor data for charts
**Parameters:**
- `capteurId` - Sensor ID (default: 1)
- `hours` - How many hours of history (default: 24)

**Example:** `get_sensor_history.php?capteurId=1&hours=24`

---

## API Endpoints Summary

### 📤 Save Sensor Data (ESP32 → Server)
**File:** `app/api/save_sensor_data.php`  
**Method:** POST  
**Headers:** `X-API-Key: smartgarden2025secret`  
**Body:**
```json
{
  "capteurId": 1,
  "temperature": 22.5,
  "humidite": 65.2
}
```
**Response:**
```json
{
  "status": "ok",
  "message": "Data saved successfully",
  "capteurId": 1,
  "temperature": 22.5,
  "humidite": 65.2
}
```

### 📥 Get Latest Reading
**File:** `app/api/get_sensor_data.php`  
**Method:** GET  
**Parameters:** `capteurId=1`  
**Response:**
```json
{
  "capteurId": 1,
  "temperature": 22.5,
  "humidite": 65.2,
  "timestamp": "2025-12-13 14:30:25"
}
```

### 📊 Get Historical Data (NEW!)
**File:** `app/api/get_sensor_history.php`  
**Method:** GET  
**Parameters:** `capteurId=1&hours=24`  
**Response:**
```json
[
  {
    "temperature": 22.5,
    "humidite": 65.2,
    "timestamp": "2025-12-13 14:00:00"
  },
  {
    "temperature": 22.8,
    "humidite": 64.5,
    "timestamp": "2025-12-13 14:05:00"
  }
]
```

### 📋 Get All Sensors
**File:** `app/api/get_all_sensors.php`  
**Method:** GET  
**Response:**
```json
[
  {
    "capteurId": 1,
    "temperature": 22.5,
    "humidite": 65.2,
    "timestamp": "2025-12-13 14:30:25"
  },
  {
    "capteurId": 2,
    "temperature": 21.0,
    "humidite": 70.5,
    "timestamp": "2025-12-13 14:30:22"
  }
]
```

---

## Database Schema

```sql
sensor_data
├── id (INT, AUTO_INCREMENT, PRIMARY KEY)
├── id_capteur (INT, NOT NULL) -- References capteur(id_capteur)
├── temperature (DECIMAL(5,2))
├── humidite (DECIMAL(5,2))
└── timestamp (DATETIME, DEFAULT CURRENT_TIMESTAMP)

Indexes:
- INDEX(id_capteur) -- Fast queries by sensor
- INDEX(timestamp)   -- Fast queries by time
```

---

## Testing

### 1. Test ESP32 → Database
Your ESP32 is already configured! Just restart it and check:
```sql
SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 10;
```
You should see new rows appearing every second (or whatever interval you set).

### 2. Test API Endpoints

**Get Latest Data:**
```
http://localhost/website/app/api/get_sensor_data.php?capteurId=1
```

**Get 24h History:**
```
http://localhost/website/app/api/get_sensor_history.php?capteurId=1&hours=24
```

**Get All Sensors:**
```
http://localhost/website/app/api/get_all_sensors.php
```

### 3. Test Frontend (plante.php)
Open `app/view/frontoffice/plante.php` in browser:
```
http://localhost/website/app/view/frontoffice/plante.php
```
It should show live temperature and humidity updating every 1 second.

---

## What Happens Now

### Data Flow:
```
ESP32 + DHT22 Sensor
    ↓ (sends every 1 second)
HTTP POST to save_sensor_data.php
    ↓ (validates API key)
INSERT INTO sensor_data table
    ↓ (stores with timestamp)
Database keeps ALL readings forever
    ↓ (query anytime)
Frontend fetches latest or historical data
```

### Benefits:
✅ **No data loss** - Everything is saved in database  
✅ **Historical tracking** - Can see trends over time  
✅ **Multiple sensors** - Database handles unlimited sensors  
✅ **Fast queries** - Indexed for performance  
✅ **Backup ready** - Standard MySQL backup works  
✅ **Analytics ready** - Can calculate AVG, MIN, MAX, etc.  

---

## Next Steps (Optional)

1. **Add Charts to Backoffice**
   - Use Chart.js to visualize 24h trends
   - Show temperature/humidity graphs

2. **Link Sensors to Plants**
   - Show plant's sensor data in plantes.php
   - Add sensor column in plant table

3. **Add Alerts**
   - Set thresholds (min/max temp, humidity)
   - Send email when values out of range

4. **Clean Old Data**
   - Delete sensor_data older than 30 days
   - Or aggregate to hourly averages

---

## Troubleshooting

**ESP32 not saving data?**
- Check if table `sensor_data` exists
- Check ESP32 Serial Monitor for errors
- Verify API key matches: `smartgarden2025secret`

**No data showing in frontend?**
- Check browser console for errors
- Verify API endpoint URLs are correct
- Check database has data: `SELECT * FROM sensor_data LIMIT 1;`

**Database errors?**
- Check config.php has correct database name
- Verify MySQL is running (XAMPP)
- Check user permissions

---

## File Changes Summary

**Modified:**
- ✏️ `app/controller/plantController.php` - Changed from file storage to database
- ✏️ `app/api/save_sensor_data.php` - Still uses controller (no change needed)
- ✏️ `app/api/get_sensor_data.php` - Still uses controller (no change needed)

**Created:**
- ➕ `app/api/get_sensor_history.php` - New endpoint for historical data

**Deprecated:**
- ❌ `storage/sensors/sensor_*.json` - No longer used (can delete folder)

---

**🎉 Integration Complete! Your ESP32 sensor data is now saving to the database!**
