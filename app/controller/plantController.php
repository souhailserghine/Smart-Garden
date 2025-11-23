<?php
class PlantController {
    
    private $storageDir;
    private $apiKey = 'smartgarden2025secret';
    
    public function __construct() {
        $this->storageDir = __DIR__ . '/../../storage/sensors';
        if (!file_exists($this->storageDir)) {
            mkdir($this->storageDir, 0777, true);
        }
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
        $timestamp = date('Y-m-d H:i:s');
        
        $sensorData = [
            'capteurId' => $capteurId,
            'temperature' => $temperature,
            'humidite' => $humidite,
            'timestamp' => $timestamp
        ];
        
        $filename = $this->storageDir . '/sensor_' . $capteurId . '.json';
        
        if (file_put_contents($filename, json_encode($sensorData, JSON_PRETTY_PRINT))) {
            echo json_encode(["status" => "ok", "message" => "Data saved successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to save data"]);
        }
    }
    
    public function getLatestData() {
        header('Content-Type: application/json');
        
        $capteurId = $_GET['capteurId'] ?? 1;
        $filename = $this->storageDir . '/sensor_' . intval($capteurId) . '.json';
        
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $data = json_decode($content, true);
            
            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Invalid data format"]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["status" => "empty", "message" => "No data available for this sensor"]);
        }
    }
    
    public function getAllSensors() {
        header('Content-Type: application/json');
        
        $sensors = [];
        $files = glob($this->storageDir . '/sensor_*.json');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            if ($data) {
                $sensors[] = $data;
            }
        }
        
        echo json_encode($sensors);
    }
}
