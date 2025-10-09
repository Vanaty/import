document.addEventListener('DOMContentLoaded', function() {
    // Navigation entre les sections
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Retirer la classe active de tous les liens
            navLinks.forEach(l => l.classList.remove('active'));
            
            // Ajouter la classe active au lien cliqué
            this.classList.add('active');
            
            // Cacher toutes les sections
            sections.forEach(section => section.classList.add('d-none'));
            
            // Afficher la section correspondante
            const sectionId = this.dataset.section + '-section';
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.remove('d-none');
            }
        });
    });

    // Gestion du formulaire d'exécution
    const pythonForm = document.getElementById('pythonForm');
    const deleteButton = document.getElementById('deleteButton');
    
    deleteButton.addEventListener('click', executeDelete);
    pythonForm.addEventListener('submit', function(e) {
        e.preventDefault();
        executeScript();
    });

    // Charger l'historique au démarrage
    loadHistory();
});

function executeDelete() {
    const form = document.getElementById('pythonForm');
    const output = document.getElementById('output');
    const statusIndicator = document.getElementById('statusIndicator');
    if (confirm('Êtes-vous sûr de vouloir supprimer les données migrées ?')) {
        // Mettre à jour le statut
        statusIndicator.innerHTML = '<span class="badge bg-danger"><i class="fas fa-trash"></i> Suppression...</span>';
        output.innerHTML = '<div class="text-info">Suppression des données en cours...</div>';

        const formData = new FormData(form);
        formData.append('action', 'delete');
        fetch('api/', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.status === 401) {
                // Session expirée, rediriger vers login
                window.location.href = 'login.php?timeout=1';
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return; // Si redirection après 401
            
            if (data.success) {
                showAlert('Données supprimées avec succès!', 'success');
                output.innerHTML = `<div class="text-success">✓ Suppression réussie</div><hr>${formatLogOutput(data.output)}`;
                statusIndicator.innerHTML = '<span class="badge bg-secondary">Prêt</span>';
            } else {
                showAlert(data.error || 'Erreur lors de la suppression', 'danger');
                output.innerHTML = `<div class="text-danger">✗ Erreur de suppression</div><hr>${formatLogOutput(data.output || data.error)}`;
                statusIndicator.innerHTML = '<span class="badge bg-danger"><i class="fas fa-times"></i> Erreur</span>';
            }
            // Recharger l'historique
            loadHistory();
        })
        .catch(error => {
            showAlert('Erreur de communication: ' + error.message, 'danger');
        });
    }
}

function executeScript() {
    const form = document.getElementById('pythonForm');
    const formData = new FormData(form);
    formData.append('action', 'execute');
    
    const output = document.getElementById('output');
    const statusIndicator = document.getElementById('statusIndicator');
    const submitButton = form.querySelector('button[type="submit"]');

    if (submitButton) {
        submitButton.disabled = true;
    }
    
    // Mettre à jour le statut
    statusIndicator.innerHTML = '<span class="badge bg-warning"><i class="loading"></i> Exécution...</span>';
    output.innerHTML = '<div class="text-info">Exécution du script en cours...</div>';
    
    fetch('api/', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.status === 401) {
            // Session expirée, rediriger vers login
            window.location.href = 'login.php?timeout=1';
            return;
        }
        return response.json();
    })
    .then(data => {
        if (!data) return; // Si redirection après 401
        
        if (data.success) {
            statusIndicator.innerHTML = '<span class="badge bg-success"><i class="fas fa-check"></i> Succès</span>';
            output.innerHTML = `<div class="text-success">✓ Exécution réussie</div><hr>${formatLogOutput(data.output)}`;
        } else {
            statusIndicator.innerHTML = '<span class="badge bg-danger"><i class="fas fa-times"></i> Erreur</span>';
            output.innerHTML = `<div class="text-danger">✗ Erreur d'exécution</div><hr>${formatLogOutput(data.output || data.error)}`;
        }
        
        // Recharger la liste des scripts et l'historique
        // refreshScriptList();
        loadHistory();
    })
    .catch(error => {
        statusIndicator.innerHTML = '<span class="badge bg-danger"><i class="fas fa-times"></i> Erreur</span>';
        output.innerHTML = `<div class="text-danger">✗ Erreur de communication: ${error.message}</div>`;
    })
    .finally(() => {
        if (submitButton) {
            submitButton.disabled = false;
        }
    });
}

function refreshScriptList() {
    // Recharger la page pour mettre à jour la liste des scripts
    // En production, ceci pourrait être fait via AJAX
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function loadHistory() {
    fetch('api/history.php')
    .then(response => {
        if (response.status === 401) {
            // Session expirée, rediriger vers login
            window.location.href = 'login.php?timeout=1';
            return;
        }
        return response.json();
    })
    .then(data => {
        if (!data) return; // Si redirection après 401
        displayHistory(data);
    })
    .catch(error => {
        console.log('Aucun historique disponible');
    });
}

function displayHistory(history) {
    const container = document.getElementById('historyContainer');
    
    if (!history || history.length === 0) {
        container.innerHTML = '<p class="text-muted">Aucun historique disponible.</p>';
        return;
    }
    
    let html = '';
    history.forEach(entry => {
        const statusClass = entry.success ? '' : 'error';
        const statusIcon = entry.success ? 'fas fa-check text-success' : 'fas fa-times text-danger';
        
        // Parse and format the output for better visualization
        const formattedOutput = formatLogOutput(entry.output);
        
        html += `
            <div class="history-item ${statusClass}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">
                        <i class="${statusIcon}"></i>
                        ${escapeHtml(entry.script)}
                    </h6>
                    <span class="timestamp">${entry.timestamp}</span>
                </div>
                ${entry.arguments ? `<p class="mb-2"><strong>Arguments:</strong> <code class="bg-light px-2 py-1 rounded">${escapeHtml(entry.arguments)}</code></p>` : ''}
                <details>
                    <summary>Voir la sortie</summary>
                    <div class="mt-2 p-3 bg-dark text-light border rounded" style="font-family: 'Courier New', monospace;">
                        ${formattedOutput}
                    </div>
                </details>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function formatLogOutput(output) {
    if (!output) return '<span class="text-muted">Aucune sortie</span>';
    
    const lines = output.split('\n');
    let formattedLines = [];
    let inHeader = true;
    
    lines.forEach(line => {
        if (line.trim() === '') {
            formattedLines.push('<br>');
            return;
        }
        
        // Format log entries with timestamps
        const logMatch = line.match(/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2},\d{3}) - (.*?) - (INFO|ERROR|WARNING|DEBUG) - (.*)$/);
        
        if (logMatch) {
            const [, timestamp, module, level, message] = logMatch;
            let levelClass = 'text-light';
            let levelIcon = '';
            
            switch (level) {
                case 'INFO':
                    levelClass = 'text-info';
                    levelIcon = '<i class="fas fa-info-circle me-1"></i>';
                    break;
                case 'ERROR':
                    levelClass = 'text-danger';
                    levelIcon = '<i class="fas fa-exclamation-triangle me-1"></i>';
                    break;
                case 'WARNING':
                    levelClass = 'text-warning';
                    levelIcon = '<i class="fas fa-exclamation-circle me-1"></i>';
                    break;
                case 'DEBUG':
                    levelClass = 'text-secondary';
                    levelIcon = '<i class="fas fa-bug me-1"></i>';
                    break;
            }
            
            formattedLines.push(`
                <div class="d-flex align-items-start mb-1">
                    <span class="${levelClass} me-2 fw-bold">${levelIcon}${level}</span>
                    <span class="text-light flex-grow-1">${escapeHtml(message)}</span>
                </div>
            `);
        } else {
            // Handle other lines (stack traces, continuation lines, etc.)
            if (line.startsWith('(') || line.startsWith(' ') || line.includes('Background on this error')) {
                formattedLines.push(`<div class="text-warning ms-4 small">${escapeHtml(line)}</div>`);
            } else {
                formattedLines.push(`<div class="text-light">${escapeHtml(line)}</div>`);
            }
        }
    });
    
    return formattedLines.join('');
}

function clearOutput() {
    const output = document.getElementById('output');
    const statusIndicator = document.getElementById('statusIndicator');
    
    output.innerHTML = '<p class="text-muted">La sortie du script apparaîtra ici...</p>';
    statusIndicator.innerHTML = '<span class="badge bg-secondary">Prêt</span>';
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insérer l'alerte en haut de la page
    const main = document.querySelector('main');
    main.insertBefore(alertDiv, main.firstChild);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
