<?php
require_once '../auth.php';

// Vérifier l'authentification
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

header('Content-Type: application/json');

$historyFile = '../history.json';
$history = [];

if (file_exists($historyFile)) {
    $history = json_decode(file_get_contents($historyFile), true) ?? [];
}

echo json_encode($history);
?>
