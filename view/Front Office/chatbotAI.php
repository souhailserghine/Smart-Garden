<?php
session_start();
include_once '../../config.php';
include_once '../../Controller/tacheC.php';
include_once '../../Controller/planteC.php';

header('Content-Type: application/json');

if (!isset($_SESSION['idUtilisateur'])) {
    $_SESSION['idUtilisateur'] = 18;
}

$userId = $_SESSION['idUtilisateur'];
$input = $_POST['message'] ?? '';

if (empty($input)) {
    echo json_encode(['error' => 'Message vide']);
    exit;
}

// Initialiser les contrôleurs
$planteC = new planteC();
$tacheC = new tacheC();

// Récupérer les données de l'utilisateur
$mesPlantes = $planteC->listPlantesByUser($userId);
$allTaches = $tacheC->listTaches();

$plantesIds = array_column($mesPlantes, 'id_plante');
$tachesUtilisateur = [];
foreach($allTaches as $t) {
    if(in_array($t['id_plante'], $plantesIds)) {
        $tachesUtilisateur[] = $t;
    }
}

// Analyser l'entrée utilisateur et générer une réponse
$response = generateAIResponse($input, $mesPlantes, $tachesUtilisateur);

echo json_encode(['response' => $response]);
exit;

function generateAIResponse($input, $plantes, $taches) {
    $input = strtolower(trim($input));
    
    // Mots-clés pour détecter l'intention
    $keywords = [
        'tache' => ['tache', 'task', 'todo', 'à faire', 'faire', 'créer'],
        'plante' => ['plante', 'plant', 'fleur', 'arbre', 'fleurs', 'arbres'],
        'conseil' => ['conseil', 'help', 'aide', 'comment', 'comment faire', 'que faire'],
        'sante' => ['santé', 'mal', 'mauvais', 'état', 'health', 'sick', 'bad'],
        'arroser' => ['arroser', 'water', 'humidité', 'soil', 'eau'],
        'lumière' => ['lumière', 'light', 'soleil', 'sun', 'ombre', 'shadow'],
        'engrais' => ['engrais', 'fertilizer', 'nutrient', 'nutrition', 'fertilisant'],
        'horaire' => ['horaire', 'schedule', 'quand', 'when', 'fréquence', 'frequency'],
    ];
    
    $detectedKeywords = [];
    foreach($keywords as $key => $words) {
        foreach($words as $word) {
            if(strpos($input, $word) !== false) {
                $detectedKeywords[] = $key;
                break;
            }
        }
    }
    
    // Générer réponse basée sur les intentions détectées
    if (empty($detectedKeywords)) {
        return generateGeneralResponse($input, $plantes, $taches);
    }
    
    // Réponses spécifiques
    $response = "";
    
    if (in_array('tache', $detectedKeywords)) {
        $response .= generateTaskAdvice($plantes, $taches);
    }
    
    if (in_array('sante', $detectedKeywords)) {
        $response .= generateHealthAdvice($plantes);
    }
    
    if (in_array('arroser', $detectedKeywords)) {
        $response .= generateWateringAdvice($plantes);
    }
    
    if (in_array('lumière', $detectedKeywords)) {
        $response .= generateLightAdvice($plantes);
    }
    
    if (in_array('engrais', $detectedKeywords)) {
        $response .= generateFertilizerAdvice($plantes);
    }
    
    if (in_array('conseil', $detectedKeywords) && in_array('plante', $detectedKeywords)) {
        $response .= generatePlantCareAdvice($plantes);
    }
    
    if (in_array('horaire', $detectedKeywords)) {
        $response .= generateScheduleAdvice($taches);
    }
    
    return !empty($response) ? $response : generateGeneralResponse($input, $plantes, $taches);
}

function generateGeneralResponse($input, $plantes, $taches) {
    $responses = [
        "Bonjour! 👋 Je suis votre assistant de jardinage IA. Comment puis-je vous aider avec vos plantes ou tâches ?",
        "Vous avez actuellement " . count($plantes) . " plante(s) et " . count($taches) . " tâche(s) en cours. Que puis-je faire pour vous ?",
        "Je peux vous aider avec : 🌱 Conseils de soins des plantes, 📋 Organisation des tâches, 💧 Arrosage, 🌞 Lumière, et bien d'autres !",
        "Dites-moi ce qui vous préoccupe et je vous proposerai des solutions personnalisées pour vos plantes ! 🌿"
    ];
    return $responses[array_rand($responses)];
}

function generateTaskAdvice($plantes, $taches) {
    $pendingTasks = array_filter($taches, function($t) { return $t['estComplete'] != 1; });
    $highPriorityTasks = array_filter($taches, function($t) { return $t['priorite'] == 'Élevée' || $t['priorite'] == 3; });
    
    $response = "📋 **Conseils pour vos tâches:**\n";
    
    if (count($highPriorityTasks) > 0) {
        $response .= "⚠️ Vous avez " . count($highPriorityTasks) . " tâche(s) de haute priorité. Je recommande de les compléter en priorité.\n";
    }
    
    if (count($pendingTasks) > 0) {
        $response .= "✅ Vous avez " . count($pendingTasks) . " tâche(s) en attente. Celles-ci concernent probablement l'arrosage ou l'entretien de vos plantes.\n";
    }
    
    $response .= "💡 Conseil : Organisez vos tâches par priorité et plante pour un meilleur suivi !";
    
    return $response;
}

function generateHealthAdvice($plantes) {
    $healthStatuses = array_count_values(array_column($plantes, 'etat_sante'));
    
    $response = "🏥 **Santé de vos plantes:**\n";
    $response .= "- Bon état: " . ($healthStatuses['Bon état'] ?? 0) . "\n";
    $response .= "- Moyen: " . ($healthStatuses['Moyen'] ?? 0) . "\n";
    $response .= "- Mauvais état: " . ($healthStatuses['Mauvais état'] ?? 0) . "\n\n";
    
    $badPlants = array_filter($plantes, function($p) { return $p['etat_sante'] == 'Mauvais état'; });
    if (count($badPlants) > 0) {
        $response .= "⚠️ Alerte: Vous avez " . count($badPlants) . " plante(s) en mauvais état. Vérifiez leur arrosage, lumière et nutriments.";
    } else {
        $response .= "✨ Excellente nouvelle! Vos plantes semblent en bonne santé. Continuez ainsi!";
    }
    
    return $response;
}

function generateWateringAdvice($plantes) {
    $response = "💧 **Conseils d'arrosage:**\n\n";
    
    foreach(array_slice($plantes, 0, 3) as $p) {
        $humiditePercent = $p['humidite'] ?? 50;
        $recommendation = "";
        
        if ($humiditePercent < 30) {
            $recommendation = "🔴 Arrosez rapidement! Le sol est très sec.";
        } elseif ($humiditePercent < 50) {
            $recommendation = "🟡 L'humidité est basse, arrosez bientôt.";
        } elseif ($humiditePercent < 70) {
            $recommendation = "🟢 L'humidité est correcte.";
        } else {
            $recommendation = "🔵 Le sol est bien humide, attendez avant d'arroser.";
        }
        
        $response .= "**" . $p['nom_plante'] . "** (" . $humiditePercent . "% humidité): " . $recommendation . "\n";
    }
    
    $response .= "\n💡 Général: Arrosez quand le sol devient sec au toucher. La plupart des plantes préfèrent un cycle mouillé-sec modéré.";
    
    return $response;
}

function generateLightAdvice($plantes) {
    $response = "🌞 **Conseils de luminosité:**\n\n";
    
    $plantTypes = array_count_values(array_column($plantes, 'type_plante'));
    
    $response .= "Vous avez:\n";
    foreach($plantTypes as $type => $count) {
        $response .= "- " . $count . " " . ($type ?? "plante(s)") . "\n";
    }
    
    $response .= "\n💡 Recommandations:\n";
    $response .= "- **Fleurs**: 6-8 heures de lumière directe par jour\n";
    $response .= "- **Plantes vertes**: 3-4 heures de lumière indirecte\n";
    $response .= "- **Cactus/Succulentes**: 8+ heures de lumière directe\n";
    $response .= "- **Plantes d'intérieur**: Lumière indirecte vive\n";
    $response .= "\nPositionnez vos plantes près d'une fenêtre appropriée et tournez-les régulièrement pour une croissance uniforme.";
    
    return $response;
}

function generateFertilizerAdvice($plantes) {
    $response = "🌱 **Conseils d'engrais et nutriments:**\n\n";
    $response .= "Pour " . count($plantes) . " plante(s):\n\n";
    
    $response .= "📅 **Fréquence recommandée:**\n";
    $response .= "- Pendant la croissance (printemps/été): 1-2 fois par semaine\n";
    $response .= "- En hiver: 1 fois par mois\n\n";
    
    $response .= "🧪 **Types d'engrais:**\n";
    $response .= "- Engrais équilibré (NPK 10-10-10): Usage général\n";
    $response .= "- Engrais azotés: Pour la croissance du feuillage\n";
    $response .= "- Engrais phosphorés: Pour les fleurs et les racines\n";
    $response .= "- Engrais potassiques: Pour la force générale\n\n";
    
    $response .= "💡 Conseil: Suivez toujours les instructions du produit et ne sur-engraissez pas!";
    
    return $response;
}

function generatePlantCareAdvice($plantes) {
    $response = "🌿 **Guide complet de soin des plantes:**\n\n";
    $response .= "✅ À faire:\n";
    $response .= "- Vérifier l'humidité du sol régulièrement\n";
    $response .= "- Assurer une bonne lumière\n";
    $response .= "- Tourner les plantes tous les 2 semaines\n";
    $response .= "- Nettoyer les feuilles délicatement\n";
    $response .= "- Rempoter quand les racines sortent du pot\n\n";
    
    $response .= "❌ À éviter:\n";
    $response .= "- Arroser excessivement (cause la pourriture)\n";
    $response .= "- Changements brusques de température\n";
    $response .= "- Courants d'air froid\n";
    $response .= "- Trop peu de lumière\n";
    $response .= "- Négliger les ravageurs\n\n";
    
    $response .= "🌡️ Conditions idéales:\n";
    $response .= "- Température: 15-25°C\n";
    $response .= "- Humidité: 40-60%\n";
    $response .= "- Lumière: Variable selon la plante";
    
    return $response;
}

function generateScheduleAdvice($taches) {
    $tachesToday = array_filter($taches, function($t) { return $t['date_dosage'] === date('Y-m-d'); });
    $tasksThisWeek = array_filter($taches, function($t) { 
        $taskDate = strtotime($t['date_dosage']);
        return $taskDate >= time() && $taskDate <= time() + (7 * 24 * 60 * 60);
    });
    
    $response = "📅 **Planification des tâches:**\n\n";
    
    if (count($tachesToday) > 0) {
        $response .= "🔔 **Aujourd'hui:** " . count($tachesToday) . " tâche(s) à faire\n";
    }
    
    if (count($tasksThisWeek) > 0) {
        $response .= "📍 **Cette semaine:** " . count($tasksThisWeek) . " tâche(s) planifiées\n";
    }
    
    $response .= "\n💡 Conseil: Planifiez vos tâches d'arrosage selon les besoins de chaque plante:\n";
    $response .= "- Plantes tropicales: 2-3 fois par semaine\n";
    $response .= "- Plantes méditerranéennes: 1 fois par semaine\n";
    $response .= "- Cactus/Succulentes: 1 fois par 2 semaines";
    
    return $response;
}
?>
