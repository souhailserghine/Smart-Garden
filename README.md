## 📖 Overview

**Smart Garden** is a full-stack IoT-enabled web application designed to help users manage their gardens intelligently. It combines real-time sensor monitoring (via ESP32 microcontrollers), AI-powered recommendations, social features, and comprehensive plant management tools.

### Key Features

- 🌡️ **Real-Time IoT Monitoring**: ESP32 sensors track temperature and humidity
- 🤖 **AI Assistant**: Groq-powered chatbot for garden advice
- 📊 **Data Analytics**: Historical sensor data visualization
- 🌿 **Plant Management**: Track plants, tasks, and maintenance schedules
- 📝 **Social Platform**: Share publications, comment, and engage with community
- 🎫 **Event System**: Create and manage garden events with reservations
- 👤 **User Management**: Complete authentication with facial recognition
- 📧 **Email Notifications**: AI-generated sensor maintenance recommendations
- 📱 **Responsive Design**: Bootstrap-based UI for all devices

---

## 🏗️ Architecture

### Tech Stack

**Backend:**
- PHP 7.4+ (MVC Architecture)
- MySQL 8.0 Database
- PDO for database operations
- RESTful API endpoints

**Frontend:**
- HTML5, CSS3, JavaScript
- Bootstrap 4/5

**IoT/Hardware:**
- ESP32 Microcontroller
- DHT22 Temperature/Humidity Sensor
- WiFi connectivity
- HTTP POST client

**AI/APIs:**
- Groq API (Chatbot LLM)
- Claude API (Sensor recommendations)
- Face Recognition API (Flask/Python)

**External Libraries:**
- PHPMailer (Email notifications)
- Google OAuth 2.0

---

## 📁 Project Structure

```
Smart-Garden/
├── app/
│   ├── api/                      # REST API endpoints
│   │   ├── save_sensor_data.php  # ESP32 sensor data ingestion
│   │   ├── get_sensor_data.php   # Latest sensor readings
│   │   ├── get_sensor_history.php # Historical data for charts
│   │   ├── get_all_sensors.php   # All sensors status
│   │   ├── reservations_api.php  # Event reservations
│   │   └── face_server.py        # Facial recognition (Flask)
│   │
│   ├── controller/               # MVC Controllers
│   │   ├── capteurC.php          # Sensor CRUD + AI recommendations
│   │   ├── planteC.php           # Plant management
│   │   ├── tacheC.php            # Task management
│   │   ├── publicationC.php      # Publications/posts
│   │   ├── CommentaireC.php      # Comments system
│   │   ├── EventController.php   # Events management
│   │   ├── ReservationController.php # Event reservations
│   │   ├── utilisateurController.php # User management
│   │   ├── plantController.php   # IoT sensor data handler
│   │   └── ModerationC.php       # Content moderation
│   │
│   ├── model/                    # MVC Models
│   │   ├── capteur.php           # Sensor entity
│   │   ├── plante.php            # Plant entity
│   │   ├── tache.php             # Task entity
│   │   ├── publication.php       # Publication entity
│   │   ├── Commentaire.php       # Comment entity
│   │   ├── Event.php             # Event entity
│   │   ├── Reservation.php       # Reservation entity
│   │   ├── Utilisateur.php       # User entity
│   │   ├── PublicationService.php # Publication business logic
│   │   └── CommentaireService.php # Comment business logic
│   │
│   ├── view/
│   │   ├── frontoffice/          # User-facing pages
│   │   │   ├── plantes.php       # Plants dashboard
│   │   │   ├── publications.php  # Social feed
│   │   │   ├── evenements.php    # Events listing
│   │   │   ├── listCapteur.php   # Sensors monitoring
│   │   │   ├── profile.php       # User profile
│   │   │   ├── settings.php      # Account settings
│   │   │   ├── sign-in.php       # Login page
│   │   │   └── sign-up.php       # Registration
│   │   │
│   │   ├── backoffice/           # Admin panel
│   │   │   ├── backoffice.php    # Dashboard
│   │   │   ├── plantes.php       # Plant management
│   │   │   ├── utilisateurs.php  # User management
│   │   │   ├── evenements.php    # Event management
│   │   │   ├── listCategorie.php # Sensor categories
│   │   │   └── reservations.php  # Reservation management
│   │   │
│   │   └── external/
│   │       └── chatgpt_api.php   # Groq API proxy
│   │
│   ├── esp32/                    # Arduino firmware
│   │   ├── smartgarden_http_post/
│   │   │   └── smartgarden_http_post.ino # Main sensor code
│   │   └── smartgarden_wifi_config/
│   │       └── smartgarden_wifi_config.ino # WiFi setup
│   │
│   ├── config.php                # Database configuration
│   ├── google_config.php         # Google OAuth config
│   ├── mailer.php                # Email utilities
│   └── password_helpers.php      # Password hashing
│
├── storage/
│   └── sensors/                  # (Legacy JSON storage)
│
├── vendor/                       # Composer dependencies
├── composer.json
├── README.md                     # This file
├── IOT_INTEGRATION_SUMMARY.md    # IoT implementation details
└── PUBLICATIONS_INTEGRATION.md   # Publications system docs
```

---

## 🚀 Installation

### Prerequisites

- **XAMPP** (Apache 2.4, MySQL 8.0, PHP 7.4+)
- **Composer** (PHP dependency manager)
- **Arduino IDE** (for ESP32 firmware)
- **ESP32 Board** + **DHT22 Sensor**
- **Python 3.x** (for facial recognition API - optional)

### Step 1: Clone Repository

```bash
cd c:\xampp\htdocs\website\
git clone https://github.com/souhailserghine/Smart-Garden.git
cd Smart-Garden
```

### Step 2: Database Setup

1. Start XAMPP (Apache + MySQL)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Create database: `smartgarden`
4. Import SQL schema:

```sql
-- Run the following SQL or import from SQL file

CREATE TABLE `capteur` (
  `id_capteur` int NOT NULL AUTO_INCREMENT,
  `etatCapteur` varchar(50) DEFAULT NULL,
  `uniteCapteur` varchar(50) DEFAULT NULL,
  `emplacement` varchar(100) DEFAULT NULL,
  `dateInstallation` date DEFAULT NULL,
  `id_categorie` int DEFAULT NULL,
  `id_plante` int DEFAULT NULL,
  PRIMARY KEY (`id_capteur`),
  KEY `id_categorie` (`id_categorie`),
  KEY `id_plante` (`id_plante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sensor_data` (
  `id_sensor_data` int NOT NULL AUTO_INCREMENT,
  `id_capteur` int NOT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `humidite` decimal(5,2) DEFAULT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sensor_data`),
  KEY `idx_capteur` (`id_capteur`),
  KEY `idx_timestamp` (`timestamp`),
  CONSTRAINT `fk_sensor_capteur` FOREIGN KEY (`id_capteur`) REFERENCES `capteur` (`id_capteur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `plante` (
  `id_plante` int NOT NULL AUTO_INCREMENT,
  `nom_plante` varchar(100) NOT NULL,
  `nom_scientifique` varchar(100) DEFAULT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `idUtilisateur` int DEFAULT NULL,
  PRIMARY KEY (`id_plante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tache` (
  `id_tache` int NOT NULL AUTO_INCREMENT,
  `id_plante` int DEFAULT NULL,
  `tache_nom` varchar(100) NOT NULL,
  `description` text,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('en_attente','en_cours','terminee') DEFAULT 'en_attente',
  `priorite` enum('basse','moyenne','haute') DEFAULT 'moyenne',
  PRIMARY KEY (`id_tache`),
  KEY `id_plante` (`id_plante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `utilisateur` (
  `idUtilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `motDePasse` varchar(255) NOT NULL,
  `dateNaissance` date DEFAULT NULL,
  `localisation` varchar(100) DEFAULT NULL,
  `face_encoding` text,
  PRIMARY KEY (`idUtilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `publication` (
  `idPublication` int NOT NULL AUTO_INCREMENT,
  `contenuTexte` text NOT NULL,
  `datePublication` datetime DEFAULT CURRENT_TIMESTAMP,
  `idUtilisateur` int NOT NULL,
  `nbLikes` int DEFAULT 0,
  `images` text,
  `videos` text,
  `statut_moderation` enum('en_attente','approuve','rejete') DEFAULT 'approuve',
  PRIMARY KEY (`idPublication`),
  KEY `idUtilisateur` (`idUtilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `commentaire` (
  `idCommentaire` int NOT NULL AUTO_INCREMENT,
  `contenuCommentaire` text NOT NULL,
  `dateCommentaire` datetime DEFAULT CURRENT_TIMESTAMP,
  `idPublication` int NOT NULL,
  `idUtilisateur` int NOT NULL,
  `statut_moderation` enum('en_attente','approuve','rejete') DEFAULT 'approuve',
  PRIMARY KEY (`idCommentaire`),
  KEY `idPublication` (`idPublication`),
  KEY `idUtilisateur` (`idUtilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categorie` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(100) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `event` (
  `id_event` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `date_event` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `max_participants` int DEFAULT NULL,
  `id_categorie` int DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_event`),
  KEY `id_categorie` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reservation` (
  `id_reservation` int NOT NULL AUTO_INCREMENT,
  `id_event` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `date_reservation` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `nombre_places` int DEFAULT 1,
  PRIMARY KEY (`id_reservation`),
  KEY `id_event` (`id_event`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

5. Or navigate to: `http://localhost/website/Smart-Garden/app/view/frontoffice/create_sensor_data_table.php` to auto-create `sensor_data` table

### Step 3: Install PHP Dependencies

```bash
cd app
composer install
```

This installs:
- PHPMailer
- Google API Client
- Firebase JWT
- Monolog
- Guzzle HTTP

### Step 4: Configure Environment

Edit `app/config.php`:

```php
self::$pdo = new PDO(
  'mysql:host=localhost;dbname=smartgarden',
  'root',        // Your MySQL username
  '',            // Your MySQL password
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
```

### Step 5: ESP32 Setup (Optional - for IoT features)

1. Install Arduino IDE
2. Add ESP32 board support:
   - File → Preferences → Additional Board Manager URLs:
   - `https://dl.espressif.com/dl/package_esp32_index.json`
3. Install libraries:
   - DHT sensor library by Adafruit
   - HTTPClient (built-in)
   - WiFi (built-in)
4. Open firmware:
   - `app/esp32/smartgarden_http_post/smartgarden_http_post.ino`
5. Configure WiFi and server:

```cpp
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
const char* serverUrl = "http://YOUR_SERVER_IP/website/Smart-Garden/app/api/save_sensor_data.php";
```

6. Connect DHT22 sensor:
   - VCC → 3.3V
   - GND → GND
   - DATA → GPIO 26
7. Upload code to ESP32

---

## 🎯 Usage

### Access the Application

**Front Office (User Interface):**
```
http://localhost/website/Smart-Garden/app/view/frontoffice/plantes.php
```

**Back Office (Admin Panel):**
```
http://localhost/website/Smart-Garden/app/view/backoffice/backoffice.php
```

### Default Credentials

Create a user via sign-up page or insert directly into database:

```sql
INSERT INTO utilisateur (nom, prenom, email, motDePasse, localisation) 
VALUES ('Admin', 'User', 'admin@smartgarden.com', '$2y$10$...', 'Garden HQ');
```

*(Use password_hash() in PHP to generate password hash)*

---

## 🌐 API Documentation

### Sensor Data Endpoints

#### 1. Save Sensor Data (ESP32 → Server)

**Endpoint:** `POST /app/api/save_sensor_data.php`

**Headers:**
```
Content-Type: application/json
X-API-Key: smartgarden2025secret
```

**Request Body:**
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
  "humidite": 65.2,
  "rows_affected": 1
}
```

#### 2. Get Latest Sensor Data

**Endpoint:** `GET /app/api/get_sensor_data.php?capteurId=1`

**Response:**
```json
{
  "capteurId": 1,
  "temperature": 22.5,
  "humidite": 65.2,
  "timestamp": "2025-12-14 10:30:25"
}
```

#### 3. Get Historical Data

**Endpoint:** `GET /app/api/get_sensor_history.php?capteurId=1&hours=24`

**Response:**
```json
[
  {
    "temperature": 22.5,
    "humidite": 65.2,
    "timestamp": "2025-12-14 10:00:00"
  },
  {
    "temperature": 23.1,
    "humidite": 64.8,
    "timestamp": "2025-12-14 10:05:00"
  }
]
```

#### 4. Get All Sensors Status

**Endpoint:** `GET /app/api/get_all_sensors.php`

**Response:**
```json
[
  {
    "capteurId": 1,
    "temperature": 22.5,
    "humidite": 65.2,
    "timestamp": "2025-12-14 10:30:25"
  },
  {
    "capteurId": 2,
    "temperature": 21.0,
    "humidite": 68.5,
    "timestamp": "2025-12-14 10:30:22"
  }
]
```

### Event & Reservation APIs

**Events:** `/app/api/reservations_api.php?action=listEvents`  
**Reservations:** `/app/api/reservations_api.php?action=listReservations`  
**Create Reservation:** `POST /app/api/reservations_api.php?action=createReservation`

---

## 🤖 AI Features

### 1. Chatbot (Groq API)

Located in: `app/view/frontoffice/plantes.php`

**Functionality:**
- Answers gardening questions
- Provides plant care advice
- Task management suggestions

**Configuration:**
Update API key in `app/view/external/chatgpt_api.php`:
```php
$apiKey = 'YOUR_GROQ_API_KEY';
```

### 2. Sensor Maintenance Recommendations (Claude API)

Located in: `app/controller/capteurC.php`

**Functionality:**
- AI-generated sensor maintenance alerts
- Priority-based recommendations
- Automated email notifications

**Configuration:**
```php
$claudeApiKey = 'YOUR_CLAUDE_API_KEY';
```

**Trigger:**
Click "Recommandation IA" button in sensor management page.

### 3. Facial Recognition Login

Located in: `app/api/face_server.py`

**Requirements:**
```bash
pip install flask face_recognition opencv-python mysql-connector-python
```

**Run Server:**
```bash
cd app/api
python face_server.py
```

**Usage:**
- Sign up with face capture
- Login using facial recognition

---

## 📊 Key Features Breakdown

### 🌿 Plant Management
- Add/Edit/Delete plants
- Scientific names and descriptions
- Image uploads
- User-specific plant collections
- AI-powered plant suggestions

### 📝 Task Management
- Create tasks for plants
- Set priorities (low/medium/high)
- Track status (pending/in-progress/completed)
- Dosage and notes tracking
- Deadline management

### 🌡️ Real-Time Sensor Monitoring
- Live temperature/humidity readings
- Auto-refresh every 1-5 seconds
- Gradient badge indicators
- Historical data charts
- Last reading timestamps

### 📱 Social Platform
- Create publications with images/videos
- Like system with AJAX
- Comment threads
- Content moderation
- User profiles

### 🎫 Event System
- Create garden events
- Category-based organization
- Participant limits
- Reservation management
- Event search and filtering

### 👥 User Management
- Registration/Login
- Profile editing
- Google OAuth integration
- Facial recognition
- Session management

---

## 🔧 Configuration Files

### Database Configuration
`app/config.php`
```php
class config {
  private static $pdo = null;
  
  public static function getConnexion() {
    if (!isset(self::$pdo)) {
      self::$pdo = new PDO(
        'mysql:host=localhost;dbname=smartgarden',
        'root',
        ''
      );
    }
    return self::$pdo;
  }
}
```

### Email Configuration
`app/mailer.php` or `app/controller/capteurC.php`
```php
$mail->Host = 'smtp.gmail.com';
$mail->Username = 'your_email@gmail.com';
$mail->Password = 'your_app_password';
$mail->Port = 587;
```

### ESP32 Configuration
`app/esp32/smartgarden_http_post/smartgarden_http_post.ino`
```cpp
const char* ssid = "YOUR_WIFI";
const char* password = "YOUR_PASSWORD";
const char* serverUrl = "http://YOUR_IP/website/Smart-Garden/app/api/save_sensor_data.php";
const char* apiKey = "smartgarden2025secret";
const int sensorId = 1;
const unsigned long sendInterval = 1000; // 1 second
```

---

## 🐛 Troubleshooting

### Issue: Database connection error
**Solution:** Check `app/config.php` credentials and ensure MySQL is running in XAMPP.

### Issue: Sensor data not appearing
**Solution:**
1. Verify `sensor_data` table exists
2. Check ESP32 Serial Monitor for connection status
3. Confirm server IP address matches in ESP32 code
4. Test endpoint: `http://localhost/website/Smart-Garden/app/view/frontoffice/test_sensor_display.php`

### Issue: Chatbot not responding
**Solution:**
1. Check Groq API key in `app/view/external/chatgpt_api.php`
2. Verify internet connection
3. Check browser console for AJAX errors

### Issue: Email notifications not working
**Solution:**
1. Use Gmail App Password (not regular password)
2. Enable 2FA in Google Account
3. Generate App Password in Security settings
4. Update `app/controller/capteurC.php` with App Password

### Issue: Emoji characters showing as `??`
**Solution:**
1. Ensure file encoding is UTF-8
2. Add to HTML `<head>`:
```html
<meta charset="UTF-8">
```
3. Check database charset is `utf8mb4`

---

## 📈 Performance Tips

1. **Database Indexing:**
   - Already indexed: `id_capteur`, `timestamp`, `idUtilisateur`, `idPublication`
   - Add more for frequently queried columns

2. **Auto-Refresh Intervals:**
   - Sensor dashboard: 1 second (configurable in `listCapteur.php`)
   - Plant dashboard: 5 seconds (configurable in `plantes.php`)
   - Adjust based on server load

3. **Image Optimization:**
   - Compress images before upload
   - Use responsive images
   - Implement lazy loading

4. **Caching:**
   - Browser caching for static assets
   - Database query caching for frequent reads

---

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open Pull Request

---

## 📝 License

This project is licensed under the MIT License.

---

## 👨‍💻 Authors

- **Souhail Serghine** - *Project Lead* - [GitHub](https://github.com/souhailserghine)

---

## 🙏 Acknowledgments

- **PHPMailer** - Email functionality
- **Bootstrap** - UI framework
- **Groq API** - AI chatbot
- **Claude API** - AI recommendations
- **ESP32 Community** - IoT documentation
- **Adafruit** - DHT sensor library

---

## 📞 Support

For issues and questions:
- Open an issue on GitHub
- Email: support@smartgarden.local

---

## 🗺️ Roadmap

### Version 2.1 (Planned)
- [ ] Mobile app (React Native)
- [ ] Multi-language support
- [ ] Weather API integration
- [ ] Plant disease detection (AI/ML)
- [ ] SMS notifications
- [ ] Advanced analytics dashboard

### Version 3.0 (Future)
- [ ] Marketplace for plants/equipment
- [ ] Social messaging system
- [ ] Automated irrigation control
- [ ] Community forums
- [ ] Gamification (badges, points)

---

## 🔐 Security

- Passwords hashed with `password_hash()` (bcrypt)
- SQL injection prevention via PDO prepared statements
- XSS protection with `htmlspecialchars()`
- CSRF protection (session-based)
- API key authentication for ESP32
- Input validation on all forms

---

## ⚙️ System Requirements

### Server Requirements
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache 2.4 (with mod_rewrite)
- 512MB RAM minimum
- 100MB disk space

### Client Requirements
- Modern web browser (Chrome, Firefox, Edge, Safari)
- JavaScript enabled
- 1920x1080 minimum resolution recommended

### Hardware Requirements (IoT)
- ESP32 Development Board
- DHT22 Temperature/Humidity Sensor
- Micro USB cable
- Breadboard and jumper wires
- WiFi network

---

