<?php
/**
 * Script de vérification et correction des fichiers frontoffice
 * - Vérifie la présence de check_session.php
 * - Vérifie le logo et le titre
 */

$frontofficeDir = __DIR__;
$files = glob($frontofficeDir . '/*.php');

$standardLogo = '<link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />';
$standardTitle = '<title>SmartGarden</title>';

$report = [];
$needsCheckSession = [];
$needsLogo = [];
$needsTitle = [];

foreach ($files as $file) {
    $filename = basename($file);
    
    // Skip utility files
    if (in_array($filename, ['check_session.php', 'authentification.php', 'logout.php', 'supprimer.php', 'modifier.php', 'like.php'])) {
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Check for DOCTYPE (only process HTML files)
    if (strpos($content, '<!DOCTYPE') === false && strpos($content, '<html') === false) {
        continue;
    }
    
    $issues = [];
    
    // Check for check_session.php
    if (strpos($content, "check_session.php") === false) {
        $issues[] = '❌ Missing check_session.php';
        $needsCheckSession[] = $filename;
    } else {
        $issues[] = '✅ Has check_session.php';
    }
    
    // Check for logo
    if (strpos($content, 'logo-16x16.png') === false) {
        $issues[] = '❌ Missing logo';
        $needsLogo[] = $filename;
    } else {
        $issues[] = '✅ Has logo';
    }
    
    // Check for SmartGarden title
    if (strpos($content, '<title>SmartGarden') !== false || strpos($content, '<title>SmartGarden') !== false) {
        $issues[] = '✅ Has SmartGarden title';
    } else {
        $issues[] = '⚠️ Different title';
        $needsTitle[] = $filename;
    }
    
    $report[$filename] = $issues;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Frontoffice Files Audit</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .summary { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .summary-item { margin: 10px 0; padding: 10px; border-left: 4px solid #2196F3; background: #e3f2fd; }
        .summary-item.warning { border-color: #ff9800; background: #fff3e0; }
        .summary-item.error { border-color: #f44336; background: #ffebee; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th { background: #2196F3; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .issue { display: block; margin: 2px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #45a049; }
    </style>
</head>
<body>
    <h1>📋 Frontoffice Files Audit Report</h1>
    
    <div class="summary">
        <h2>Summary</h2>
        <div class="summary-item">
            <strong>Total files checked:</strong> <?= count($report) ?>
        </div>
        <div class="summary-item error">
            <strong>Files missing check_session.php:</strong> <?= count($needsCheckSession) ?>
            <?php if (!empty($needsCheckSession)): ?>
                <ul>
                    <?php foreach ($needsCheckSession as $f): ?>
                        <li><?= $f ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="summary-item warning">
            <strong>Files missing logo:</strong> <?= count($needsLogo) ?>
            <?php if (!empty($needsLogo)): ?>
                <ul>
                    <?php foreach ($needsLogo as $f): ?>
                        <li><?= $f ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="summary-item warning">
            <strong>Files with different title:</strong> <?= count($needsTitle) ?>
            <?php if (!empty($needsTitle)): ?>
                <ul>
                    <?php foreach ($needsTitle as $f): ?>
                        <li><?= $f ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    
    <h2>Detailed Report</h2>
    <table>
        <thead>
            <tr>
                <th>File</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report as $file => $issues): ?>
                <tr>
                    <td><strong><?= $file ?></strong></td>
                    <td>
                        <?php foreach ($issues as $issue): ?>
                            <span class="issue"><?= $issue ?></span>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <br>
    <a href="publications.php" class="btn">← Back to Publications</a>
</body>
</html>
