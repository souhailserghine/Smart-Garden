<?php
// run_migrations.php — exécute les migrations SQL (création table categorie + seed)
// Usage : ouvrez dans le navigateur -> http://localhost/view/Backend/migrations/run_migrations.php

require_once __DIR__ . '/../app/core/Database.php';

$db = new Database();
$pdo = $db->connect();

$files = [
    __DIR__ . '/add_categorie.sql',
    __DIR__ . '/seed_categorie.sql'
];
// Add events seed
$files[] = __DIR__ . '/seed_events.sql';

$results = [];

foreach ($files as $file) {
    if (!file_exists($file)) {
        $results[] = ["file" => $file, "status" => "missing"];
        continue;
    }
    $sql = file_get_contents($file);
    // Naive split on semicolon to execute statements individually
    $stmts = array_filter(array_map('trim', explode(';', $sql)));
    $fileRes = [];
    foreach ($stmts as $stmt) {
        if ($stmt === '') continue;
        try {
            $pdo->exec($stmt);
            $fileRes[] = ["stmt" => substr($stmt,0,120), "status" => "ok"];
        } catch (PDOException $ex) {
            $fileRes[] = ["stmt" => substr($stmt,0,120), "status" => "error", "error" => $ex->getMessage()];
        }
    }
    $results[] = ["file" => basename($file), "results" => $fileRes];
}

?><!doctype html>
<html><head><meta charset="utf-8"><title>Run Migrations</title>
<style>body{font-family:Helvetica,Arial;padding:20px}pre{background:#f6f8fa;padding:10px;border-radius:4px}</style>
</head><body>
<h2>Run Migrations Results</h2>
<?php foreach($results as $r): ?>
    <h3><?php echo htmlspecialchars($r['file']); ?></h3>
    <?php if(isset($r['status']) && $r['status'] === 'missing'): ?>
        <p style="color:crimson">Fichier manquant: <?php echo htmlspecialchars($r['file']); ?></p>
    <?php else: ?>
        <ul>
        <?php foreach($r['results'] as $s): ?>
            <li>
                <strong><?php echo htmlspecialchars($s['status']); ?></strong>
                - <code><?php echo htmlspecialchars($s['stmt']); ?></code>
                <?php if(isset($s['error'])): ?>
                    <div style="color:crimson"><small><?php echo htmlspecialchars($s['error']); ?></small></div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endforeach; ?>

<p>Après exécution, vérifiez <a href="../public/index.php?action=categories">l'endpoint categories</a> et rechargez la page Back Office.</p>
</body></html>
