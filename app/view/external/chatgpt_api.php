<?php
session_start();
header('Content-Type: application/json');

// Charger les variables d'environnement
require_once __DIR__ . '/config_env.php';

// Récupérer les clés Groq depuis .env
$apiKey = getenv('GROQ_API_KEY');
$model = getenv('GROQ_MODEL') ?: 'llama-3.3-70b-versatile';
$temperature = (float)(getenv('GROQ_TEMPERATURE') ?: '0.7');
$maxTokens = (int)(getenv('GROQ_MAX_TOKENS') ?: '2000');

define('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');

try {
    // DEBUG: Log les variables d'environnement dans le log serveur (jamais afficher côté client !)
    error_log('DEBUG GROQ_API_KEY: ' . $apiKey);
    error_log('DEBUG GROQ_MODEL: ' . $model);
    error_log('DEBUG GROQ_TEMPERATURE: ' . $temperature);
    error_log('DEBUG GROQ_MAX_TOKENS: ' . $maxTokens);
    // Vérifier que l'API key est configurée
    if (empty($apiKey) || strpos($apiKey, 'gsk_') === false) {
        throw new Exception('Clé Groq non configurée. Obtiens une clé gratuite sur https://console.groq.com/keys');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['message'])) {
        throw new Exception('Message manquant');
    }

    $userMessage = trim($input['message']);
    if (empty($userMessage)) {
        throw new Exception('Message vide');
    }

    // System prompt pour expertise en jardinage
    $systemPrompt = "Tu es un assistant expert en jardinage et en gestion des plantes d'intérieur. Tu donnes des conseils pratiques et détaillés sur:
- L'arrosage et l'humidité des plantes
- Les engrais et les traitements
- Les problèmes de santé des plantes
- Les types de dosages et modes d'application
- L'entretien général des plantes

Réponds toujours en français et sois bref mais complet (2-3 paragraphes max).";

    $requestData = [
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userMessage]
        ],
        'temperature' => $temperature,
        'max_tokens' => $maxTokens
    ];

    // Appel à l'API Groq
    $ch = curl_init(GROQ_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false, // Ignorer la vérification SSL pour debug
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception('Erreur cURL: ' . $curlError);
    }

    if ($httpCode !== 200) {
        $errorResponse = json_decode($response, true);
        $errorMsg = 'Erreur inconnue';
        if (isset($errorResponse['error']['message'])) {
            $errorMsg = $errorResponse['error']['message'];
        }
        throw new Exception('Erreur Groq (' . $httpCode . '): ' . $errorMsg);
    }

    $result = json_decode($response, true);
    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception('Réponse invalide de Groq');
    }

    $aiMessage = $result['choices'][0]['message']['content'];

    echo json_encode([
        'success' => true,
        'message' => $aiMessage
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
