document.addEventListener('DOMContentLoaded', function() {
    // Sidebar responsive functionality
    initSidebarToggle();
    
    // Initialize date selection functionality
    initDateSelection();
    
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
            
            // Fermer le sidebar sur mobile après sélection
            if (window.innerWidth < 768) {
                closeSidebar();
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

function initDateSelection() {
    const singleDateRadio = document.getElementById('singleDate');
    const dateRangeRadio = document.getElementById('dateRange');
    const singleDateContainer = document.getElementById('singleDateContainer');
    const dateRangeContainer = document.getElementById('dateRangeContainer');
    const dateInput = document.getElementById('dateInput');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    // Gérer le changement de type de date
    function toggleDateInputs() {
        if (singleDateRadio.checked) {
            singleDateContainer.classList.remove('d-none');
            dateRangeContainer.classList.add('d-none');
            dateInput.required = true;
            startDate.required = false;
            endDate.required = false;
        } else {
            singleDateContainer.classList.add('d-none');
            dateRangeContainer.classList.remove('d-none');
            dateInput.required = false;
            startDate.required = true;
            endDate.required = true;
        }
    }

    singleDateRadio.addEventListener('change', toggleDateInputs);
    dateRangeRadio.addEventListener('change', toggleDateInputs);

    // Validation de la plage de dates
    startDate.addEventListener('change', function() {
        if (endDate.value && startDate.value > endDate.value) {
            endDate.value = startDate.value;
        }
        endDate.min = startDate.value;
    });

    endDate.addEventListener('change', function() {
        if (startDate.value && endDate.value < startDate.value) {
            startDate.value = endDate.value;
        }
        startDate.max = endDate.value;
    });

    // Initialiser l'état
    toggleDateInputs();
}

function initSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (!sidebarToggle || !sidebar) return;
    
    // Créer l'overlay pour mobile
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    overlay.id = 'sidebarOverlay';
    document.body.appendChild(overlay);
    
    // Toggle sidebar
    sidebarToggle.addEventListener('click', function() {
        toggleSidebar();
    });
    
    // Fermer sidebar en cliquant sur l'overlay
    overlay.addEventListener('click', function() {
        closeSidebar();
    });
    
    // Fermer sidebar avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });
    
    // Gérer le redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeSidebar();
        }
    });
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar.classList.contains('show')) {
        closeSidebar();
    } else {
        openSidebar();
    }
}

function openSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.add('show');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
    document.body.style.overflow = '';
}

function executeDelete() {
    const form = document.getElementById('pythonForm');
    const output = document.getElementById('output');
    const statusIndicator = document.getElementById('statusIndicator');
    
    // Validation des dates
    if (!validateDates()) {
        return;
    }
    
    // Message de confirmation personnalisé selon le type de date
    const dateType = document.querySelector('input[name="dateType"]:checked').value;
    let confirmMessage = 'Êtes-vous sûr de vouloir supprimer les données migrées ?';
    
    if (dateType === 'range') {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
        confirmMessage = `Êtes-vous sûr de vouloir supprimer les données migrées pour la période du ${startDate} au ${endDate} (${diffDays} jour${diffDays > 1 ? 's' : ''}) ?`;
    }
    
    if (confirm(confirmMessage)) {
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
    
    // Validation des dates
    if (!validateDates()) {
        return;
    }
    
    const formData = new FormData(form);
    formData.append('action', 'execute');
    
    const output = document.getElementById('output');
    const statusIndicator = document.getElementById('statusIndicator');
    const submitButton = form.querySelector('button[type="submit"]');
    const dateType = formData.get('dateType');

    if (submitButton) {
        submitButton.disabled = true;
    }

    const dates = [];
    if (dateType === 'range') {
        const startDate = formData.get('start_date');
        const endDate = formData.get('end_date');
        dates.push(...getRangeDates(startDate, endDate));
    } else {
        const dateInput = formData.get('date');
        dates.push(dateInput);
    }

    try {
        for (const date of dates) {
            formData.set('date', date); // Mettre à jour la date dans le FormData
            // Mettre à jour le statut
            statusIndicator.innerHTML = '<span class="badge bg-warning"><i class="loading"></i> Exécution...</span>';
            output.innerHTML = `<div class="text-info">Exécution du script pour la date ${date}</div>`;
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
            });
        }
    } finally {
        if (submitButton) {
            submitButton.disabled = false;
        }
    }
}

function getRangeDates(startDate, endDate) {
    const dates = [];
    let currentDate = new Date(startDate);
    const lastDate = new Date(endDate);

    while (currentDate <= lastDate) {
        dates.push(currentDate.toISOString().split('T')[0]);
        currentDate.setDate(currentDate.getDate() + 1);
    }
    return dates;
}

function validateDates() {
    const dateType = document.querySelector('input[name="dateType"]:checked').value;
    
    if (dateType === 'single') {
        const dateInput = document.getElementById('dateInput');
        if (!dateInput.value) {
            showAlert('Veuillez sélectionner une date.', 'warning');
            return false;
        }
    } else {
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        
        if (!startDate.value || !endDate.value) {
            showAlert('Veuillez sélectionner une date de début et une date de fin.', 'warning');
            return false;
        }
        
        if (startDate.value > endDate.value) {
            showAlert('La date de début doit être antérieure ou égale à la date de fin.', 'warning');
            return false;
        }
        
        // Calculer le nombre de jours
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        if (diffDays > 5) {
            if (!confirm(`Vous avez sélectionné ${diffDays} jours. Cela peut prendre beaucoup de temps. Voulez-vous continuer ?`)) {
                return false;
            }
        }
    }
    
    return true;
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
        
        // Déterminer l'icône et le libellé pour le type d'action
        let actionIcon = 'fas fa-play';
        let actionLabel = 'Import';
        let actionClass = 'text-primary';
        
        if (entry.action_type === 'delete') {
            actionIcon = 'fas fa-trash';
            actionLabel = 'Suppression';
            actionClass = 'text-danger';
        }
        
        // Parse and format the output for better visualization
        const formattedOutput = formatLogOutput(entry.output);
        
        html += `
            <div class="history-item ${statusClass}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">
                        <i class="${statusIcon}"></i>
                        <i class="${actionIcon} ${actionClass} me-1"></i>
                        ${actionLabel} - ${escapeHtml(entry.script)}
                    </h6>
                    <span class="timestamp">${entry.timestamp}</span>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            Utilisateur: <strong>${escapeHtml(entry.user)}</strong>
                        </small>
                    </div>
                    ${entry.import_date ? `
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            ${entry.import_date.includes('à') ? 'Période:' : 'Date d\'import:'} <strong>${entry.import_date}</strong>
                        </small>
                    </div>
                    ` : ''}
                </div>
                ${entry.group_id ? `
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="fas fa-users me-1"></i>
                        Groupe: <strong>${escapeHtml(entry.group_id)}</strong>
                    </small>
                </div>
                ` : ''}
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
