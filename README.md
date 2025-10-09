# Interface d'Exécution de Scripts Python

Cette application web fournit une interface utilisateur pour exécuter des scripts Python et visualiser leur sortie. Elle conserve également un historique des exécutions.

## Fonctionnalités

- Interface web simple pour lancer des scripts.
- Sélection de la date et du groupe pour l'import.
- Affichage en temps réel de la sortie du script.
- Indicateur de statut (en cours, succès, erreur).
- Historique des 31 dernières exécutions avec détails (arguments, sortie, statut).
- Design responsive basé sur Bootstrap.

## Structure du Projet

```
import/
├── api/
│   └── index.php           # Endpoint AJAX pour l'exécution des scripts
├── scripts/
│   ├── traccar-data/       # Script d'import de données Traccar
│   │   ├── main.py
│   │   └── ...
│   └── test/               # Script d'exemple
│       └── main.py
├── venv/                   # Environnement virtuel Python (à créer)
├── history.json            # Fichier de stockage de l'historique
├── index.php               # Fichier principal de l'interface utilisateur
├── script.js               # Logique JavaScript du frontend
├── style.css               # Styles CSS
└── README.md               # Ce fichier
```

## Installation

### Prérequis

- Un serveur web avec PHP (par exemple XAMPP, WAMP, ou autre).
- Python 3.x

### Étapes

1.  **Cloner ou télécharger le projet** dans le répertoire de votre serveur web (ex: `htdocs` pour XAMPP).

2.  **Créer l'environnement virtuel Python** à la racine du projet `import/` :
    ```bash
    cd chemin/vers/import
    python -m venv venv
    ```

3.  **Activer l'environnement virtuel** :
    - Sur Windows :
      ```bash
      .\venv\Scripts\activate
      ```
    - Sur macOS/Linux :
      ```bash
      source venv/bin/activate
      ```

4.  **Installer les dépendances Python** pour chaque script. Par exemple, pour `traccar-data` :
    ```bash
    pip install -r scripts/traccar-data/requirements.txt
    ```

5.  **Configurer les scripts** : Chaque script dans le dossier `scripts/` peut nécessiter son propre fichier `.env` pour les variables d'environnement. Copiez le fichier `.env.example` en `.env` et ajustez les valeurs.
    - Pour `traccar-data` :
      ```bash
      cp scripts/traccar-data/.env.example scripts/traccar-data/.env
      ```
      Modifiez ensuite `scripts/traccar-data/.env` avec vos configurations.

6.  **Permissions** : Assurez-vous que le serveur web a les permissions nécessaires pour écrire dans le fichier `history.json` et exécuter les scripts Python.

## Utilisation

1.  Accédez à l'application via votre navigateur (ex: `http://localhost/import/`).
2.  La page principale "Importer Fichier" s'affiche.
3.  Sélectionnez un groupe et une date d'import.
4.  Cliquez sur "Lancer l'Import".
5.  La sortie du script s'affichera dans le panneau "Sortie".
6.  Consultez l'onglet "Historique" pour voir les exécutions passées.

## Personnalisation

Pour changer le script exécuté par défaut, modifiez la ligne suivante dans `index.php` :

```php
// Dans index.php
<input type="hidden" name="script" value="test">
```

Remplacez `test` par le nom du dossier du script que vous souhaitez exécuter (par exemple `traccar-data`).
