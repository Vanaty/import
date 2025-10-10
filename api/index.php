<?php
    require_once '../auth.php';
    require_once '../database.php';
    
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
        global $userDB;
        
        $script = $_POST['script'] ?? '';
        $arguments = $_POST['arguments'] ?? '';
        $importDate = $_POST['date'] ?? null;
        $groupId = $_POST['group'] ?? null;
        $actionType = ($_POST['action'] ?? 'execute') === 'delete' ? 'delete' : 'execute';
        
        // Récupérer les informations de l'utilisateur connecté
        $username = getUsername();
        $userInfo = $userDB->getUserInfo($username);
        
        if (!$userInfo) {
            echo json_encode(['success' => false, 'error' => 'Impossible de récupérer les informations utilisateur']);
            return;
        }
        
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

        $scriptPath = '../scripts/' . 'test' . '/main.py';
        if (!file_exists($scriptPath)) {
            echo json_encode(['success' => false, 'error' => 'Script non trouvé']);
            return;
        }

        // Construire la commande
        $pythonExecutable = null;
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

        $outputString = implode("\n", $output);
        $success = $returnVar === 0;

        // Enregistrer dans l'historique de la base de données
        $saveResult = $userDB->saveImportHistory(
            $userInfo['id'],
            $username,
            $script,
            $arguments,
            $outputString,
            $success,
            $importDate,
            $groupId,
            $actionType
        );

        echo json_encode([
            'success' => $success,
            'output' => $outputString,
            'returnCode' => $returnVar
        ], JSON_INVALID_UTF8_SUBSTITUTE);
    }
    ?>