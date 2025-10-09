<?php
    require_once '../auth.php';
    
    // Vérifier l'authentification pour toutes les requêtes API
    if (!isLoggedIn()) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Non autorisé - Veuillez vous connecter']);
        exit;
    }
    
    // Traitement des requêtes AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'execute':
                    executeScript();
                    break;
                case 'delete':
                    executeScript();
                    break;
            }
        }
        exit;
    }

    function executeScript() {
        $script = $_POST['script'] ?? '';
        $arguments = $_POST['arguments'] ?? '';
        
        if (!empty($_POST['date'])) {
            $arguments .= ' --date ' . escapeshellarg($_POST['date']);
        }
        if (!empty($_POST['group'])) {
            $arguments .= ' --group-id ' . escapeshellarg($_POST['group']);
        }
        if (!empty($_POST['action']) && $_POST['action'] === 'delete') {
            if (empty($_POST['date'])) {
                echo json_encode(['success' => false, 'error' => 'Date requise pour la suppression']);
                return;
            }
            $arguments = ' --delete ' . escapeshellarg($_POST['date']);
        }
        if (empty($script)) {
            echo json_encode(['success' => false, 'error' => 'Aucun script sélectionné']);
            return;
        }

        $scriptPath = '../scripts/' . $script . '/main.py';
        if (!file_exists($scriptPath)) {
            echo json_encode(['success' => false, 'error' => 'Script non trouvé']);
            return;
        }

        // Construire la commande
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $pythonExecutable = realpath('../venv/Scripts/python.exe');
        } else {
            $pythonExecutable = realpath('../venv/bin/python');
        }

        if (!$pythonExecutable) {
            echo json_encode(['success' => false, 'error' => 'Environnement virtuel Python non trouvé']);
            return;
        }
        $command = "\"$pythonExecutable\" \"$scriptPath\"";
        if (!empty($arguments)) {
            $command .= $arguments;
        }

        // Exécuter le script
        $output = [];
        $returnVar = 0;
        exec($command . " 2>&1", $output, $returnVar);

        // Enregistrer dans l'historique
        $historyEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'script' => $script,
            'arguments' => $arguments,
            'output' => implode("\n", $output),
            'success' => $returnVar === 0
        ];
        saveToHistory($historyEntry);

        echo json_encode([
            'success' => $returnVar === 0,
            'output' => implode("\n", $output),
            'returnCode' => $returnVar
        ]);
    }

    function saveToHistory($entry) {
        $historyFile = '../history.json';
        $history = [];
        
        if (file_exists($historyFile)) {
            $history = json_decode(file_get_contents($historyFile), true) ?? [];
        }
        
        array_unshift($history, $entry);
        $history = array_slice($history, 0, 31); // Garder seulement les 31 dernières entrées
        
        file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT));
    }
    ?>