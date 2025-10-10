<?php
require_once '../auth.php';
require_once '../database.php';

// Vérifier l'authentification
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

header('Content-Type: application/json');

global $userDB;

// Récupérer l'historique depuis la base de données
$history = $userDB->getImportHistory(50);
// Formater l'historique pour être compatible avec l'affichage existant
$formattedHistory = [];
foreach ($history as $entry) {
    $formattedHistory[] = [
        'id' => $entry['id'],
        'timestamp' => $entry['server_date'],
        'script' => $entry['script_name'],
        'arguments' => $entry['arguments'],
        'output' => $entry['output'],
        'success' => (bool)$entry['success'],
        'user' => $entry['username'],
        'import_date' => $entry['import_date'],
        'group_id' => $entry['group_id'],
        'action_type' => $entry['action_type']
    ];
}
echo json_encode($formattedHistory, JSON_INVALID_UTF8_SUBSTITUTE);
?>
