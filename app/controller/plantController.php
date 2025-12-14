<?php
require_once __DIR__ . '/../config.php';

class PlantController {
    
    private $apiKey = 'smartgarden2025secret';
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    public function saveSensorData() {
        header('Content-Type: application/json');
        
        $headers = getallheaders();
        $apiKey = $headers['X-API-Key'] ?? $_POST['apiKey'] ?? '';
        
        if ($apiKey !== $this->apiKey) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Unauthorized"]);
            return;
        }
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        if (!isset($data['capteurId']) || !isset($data['temperature']) || !isset($data['humidite'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing required fields: capteurId, temperature, humidite"]);
            return;
        }
        
        $capteurId = intval($data['capteurId']);
        $temperature = floatval($data['temperature']);
        $humidite = floatval($data['humidite']);
        
        try {
            // Log pour debug
            error_log("=== SAVE SENSOR DATA ===");
            error_log("Capteur ID: $capteurId");
            error_log("Temperature: $temperature");
            error_log("Humidite: $humidite");
            
            // Save to database
            $stmt = $this->db->prepare('INSERT INTO sensor_data (id_capteur, temperature, humidite) VALUES (?, ?, ?)');
            $result = $stmt->execute([$capteurId, $temperature, $humidite]);
            
            error_log("Execute result: " . ($result ? 'true' : 'false'));
            error_log("Rows affected: " . $stmt->rowCount());
            
            if ($result) {
                error_log("✅ Data saved successfully!");
                echo json_encode([
                    "status" => "ok", 
                    "message" => "Data saved successfully",
                    "capteurId" => $capteurId,
                    "temperature" => $temperature,
                    "humidite" => $humidite,
                    "rows_affected" => $stmt->rowCount()
                ]);
            } else {
                error_log("❌ Failed to save data");
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to save data"]);
            }
        } catch (Exception $e) {
            error_log("❌ Exception: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    }
    
    public function getLatestData() {
        header('Content-Type: application/json');
        
        $capteurId = intval($_GET['capteurId'] ?? 1);
        
        try {
            // Get latest reading from database
            $stmt = $this->db->prepare('SELECT id_capteur as capteurId, temperature, humidite, timestamp 
                                        FROM sensor_data 
                                        WHERE id_capteur = ? 
                                        ORDER BY timestamp DESC 
                                        LIMIT 1');
            $stmt->execute([$capteurId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "empty", "message" => "No data available for this sensor"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    }
    
    public function getAllSensors() {
        header('Content-Type: application/json');
        
        try {
            // Get latest reading for each sensor
            $stmt = $this->db->query('SELECT s1.id_capteur as capteurId, s1.temperature, s1.humidite, s1.timestamp
                                      FROM sensor_data s1
                                      INNER JOIN (
                                          SELECT id_capteur, MAX(timestamp) as max_timestamp
                                          FROM sensor_data
                                          GROUP BY id_capteur
                                      ) s2 ON s1.id_capteur = s2.id_capteur AND s1.timestamp = s2.max_timestamp
                                      ORDER BY s1.id_capteur');
            
            $sensors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($sensors);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    }
}
