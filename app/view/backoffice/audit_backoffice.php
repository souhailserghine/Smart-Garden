<?php
/**
 * Audit Backoffice Files - Check logo and title consistency
 */

$directory = __DIR__;
$files = glob($directory . '/*.php');

// Standard values
$standardLogo = '<link rel="icon" type="image/png" href="img/logo-16x16.png" />';
$standardTitle = '<title>SmartGarden</title>';

echo "<h2>Backoffice Files Audit Report</h2>";
echo "<p>Standard Logo: <code>" . htmlspecialchars($standardLogo) . "</code></p>";
echo "<p>Standard Title: <code>" . htmlspecialchars($standardTitle) . "</code></p>";
echo "<hr>";

$missingLogo = [];
$wrongTitle = [];
$correctFiles = [];

foreach ($files as $file) {
    $filename = basename($file);
    
    // Skip utility files
    if (in_array($filename, ['check_session.php', 'logout.php', 'audit_backoffice.php', 
                             'getPlanteDetails.php', 'getPlantes.php', 'get_users.php',
                             'supprimerPlante.php', 'supprimerTache.php', 'deleteCapteur.php',
                             'deleteCategorie.php', 'delete_user.php', 'delete_historique_action.php',
                             'toggle_user_status.php', 'password_reset_helpers.php',
                             'header_back.php', 'exporter_excel.php', 'forget_password.php',
                             'suggestionAPI.php', 'suggestionTacheAPI.php', 'modifierPlante.php',
                             'modifierTache.php'])) {
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Check if it's an HTML file (has <html> or <!DOCTYPE)
    if (stripos($content, '<html') === false && stripos($content, '<!DOCTYPE') === false) {
        continue;
    }
    
    // Check for logo
    $hasLogo = (stripos($content, 'rel="icon"') !== false || stripos($content, "rel='icon'") !== false);
    $hasCorrectLogo = (stripos($content, 'logo-16x16.png') !== false);
    
    // Check for title
    preg_match('/<title>(.*?)<\/title>/i', $content, $titleMatch);
    $currentTitle = $titleMatch[1] ?? 'NO TITLE';
    
    // Categorize
    if (!$hasLogo || !$hasCorrectLogo) {
        $missingLogo[] = $filename;
    }
    
    if ($currentTitle !== 'SmartGarden') {
        $wrongTitle[] = [
            'file' => $filename,
            'current' => $currentTitle
        ];
    }
    
    if ($hasCorrectLogo && $currentTitle === 'SmartGarden') {
        $correctFiles[] = $filename;
    }
}

// Display results
echo "<h3>Summary</h3>";
echo "<p><strong>Files missing correct logo:</strong> " . count($missingLogo) . "</p>";
echo "<p><strong>Files with wrong title:</strong> " . count($wrongTitle) . "</p>";
echo "<p><strong>Files fully compliant:</strong> " . count($correctFiles) . "</p>";

if (!empty($missingLogo)) {
    echo "<h3>Files Missing Correct Logo (" . count($missingLogo) . ")</h3>";
    echo "<ul>";
    foreach ($missingLogo as $file) {
        echo "<li><code>$file</code></li>";
    }
    echo "</ul>";
}

if (!empty($wrongTitle)) {
    echo "<h3>Files with Wrong Title (" . count($wrongTitle) . ")</h3>";
    echo "<ul>";
    foreach ($wrongTitle as $item) {
        echo "<li><code>{$item['file']}</code> - Current: <strong>" . htmlspecialchars($item['current']) . "</strong></li>";
    }
    echo "</ul>";
}

if (!empty($correctFiles)) {
    echo "<h3>Fully Compliant Files (" . count($correctFiles) . ")</h3>";
    echo "<ul>";
    foreach ($correctFiles as $file) {
        echo "<li style='color: green;'><code>$file</code> ✓</li>";
    }
    echo "</ul>";
}
?>
