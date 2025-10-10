print("""
2025-07-15 10:18:00,448 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 10:18:00,478 - utils.logger - INFO - Migration complète vers parcours pour la date 2025-07-15
2025-07-15 10:18:00,478 - utils.logger - INFO - Début de la migration API Traccar vers parcours pour la date: 2025-07-15, groupe: 8
2025-07-15 10:18:00,479 - utils.logger - INFO - Appel API avec paramètres: {'from': '2025-07-15T00:00:00.000Z', 'to': '2025-07-15T23:59:59.999Z', 'daily': 'false', 'groupId': 8}
2025-07-15 10:18:04,543 - utils.logger - ERROR - Erreur lors de l'appel API: HTTPConnectionPool(host='localhost', port=8082): Max retries exceeded with url: /api/reports/summary?from=2025-07-15T00%3A00%3A00.000Z&to=2025-07-15T23%3A59%3A59.999Z&daily=false&groupId=8 (Caused by NewConnectionError('<urllib3.connection.HTTPConnection object at 0x000001CB72DBF0E0>: Failed to establish a new connection: [WinError 10061] Aucune connexion n’a pu être établie car l’ordinateur cible l’a expressément refusée'))
2025-07-15 10:18:04,546 - utils.logger - ERROR - Opération échouée
2025-07-15 10:18:28,216 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 10:18:28,242 - utils.logger - INFO - Migration complète vers parcours pour la date 2025-07-15
2025-07-15 10:18:28,243 - utils.logger - INFO - Début de la migration API Traccar vers parcours pour la date: 2025-07-15, groupe: 8
2025-07-15 10:18:28,243 - utils.logger - INFO - Appel API avec paramètres: {'from': '2025-07-15T00:00:00.000Z', 'to': '2025-07-15T23:59:59.999Z', 'daily': 'false', 'groupId': 8}
2025-07-15 10:18:32,315 - utils.logger - ERROR - Erreur lors de l'appel API: HTTPConnectionPool(host='localhost', port=8082): Max retries exceeded with url: /api/reports/summary?from=2025-07-15T00%3A00%3A00.000Z&to=2025-07-15T23%3A59%3A59.999Z&daily=false&groupId=8 (Caused by NewConnectionError('<urllib3.connection.HTTPConnection object at 0x000001BD9C7FF0E0>: Failed to establish a new connection: [WinError 10061] Aucune connexion n’a pu être établie car l’ordinateur cible l’a expressément refusée'))
2025-07-15 10:18:32,315 - utils.logger - ERROR - Opération échouée
2025-07-15 10:55:41,110 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 10:55:41,114 - utils.logger - ERROR - Erreur critique: invalid literal for int() with base 10: 'None'
2025-07-15 10:55:50,391 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 10:55:50,393 - utils.logger - ERROR - Erreur critique: invalid literal for int() with base 10: 'None'
2025-07-15 10:56:09,840 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 10:56:09,842 - utils.logger - ERROR - Erreur critique: invalid literal for int() with base 10: 'None'
2025-07-15 10:56:48,302 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 10:56:48,304 - utils.logger - ERROR - Erreur critique: invalid literal for int() with base 10: 'None'
2025-07-15 10:58:36,794 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 10:59:49,694 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 11:01:24,040 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 11:02:03,868 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 11:02:59,317 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 11:03:41,256 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 11:03:41,286 - utils.logger - INFO - Migration complète vers parcours pour la date 2025-07-12
2025-07-15 11:03:41,287 - utils.logger - INFO - Début de la migration API Traccar vers parcours pour la date: 2025-07-12, groupe: 8
2025-07-15 11:03:41,287 - utils.logger - INFO - Appel API avec paramètres: {'from': '2025-07-12T00:00:00.000Z', 'to': '2025-07-12T23:59:59.999Z', 'daily': 'false', 'groupId': 8}
2025-07-15 11:04:40,318 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 11:04:40,351 - utils.logger - INFO - Migration complète vers parcours pour la date 2025-07-12
2025-07-15 11:04:40,351 - utils.logger - INFO - Début de la migration API Traccar vers parcours pour la date: 2025-07-12, groupe: 8
2025-07-15 11:04:40,352 - utils.logger - INFO - Appel API avec paramètres: {'from': '2025-07-12T00:00:00.000Z', 'to': '2025-07-12T23:59:59.999Z', 'daily': 'false', 'groupId': 8}
2025-07-15 11:05:27,536 - utils.logger - INFO - Données reçues de l'API: 111 enregistrements
2025-07-15 11:05:27,545 - utils.logger - ERROR - Erreur lors de la migration: (pymysql.err.OperationalError) (1045, "Access denied for user 'root'@'localhost' (using password: YES)")
(Background on this error at: https://sqlalche.me/e/20/e3q8)
2025-07-15 11:05:27,546 - utils.logger - ERROR - Opération échouée
2025-07-15 11:07:36,775 - utils.logger - INFO - Démarrage de l'application de migration
2025-07-15 11:07:36,804 - utils.logger - INFO - Migration complète vers parcours pour la date 2025-07-12
2025-07-15 11:07:36,804 - utils.logger - INFO - Début de la migration API Traccar vers parcours pour la date: 2025-07-12, groupe: 8
2025-07-15 11:07:36,805 - utils.logger - INFO - Appel API avec paramètres: {'from': '2025-07-12T00:00:00.000Z', 'to': '2025-07-12T23:59:59.999Z', 'daily': 'false', 'groupId': 8}
2025-07-15 11:08:20,056 - utils.logger - INFO - Données reçues de l'API: 111 enregistrements
2025-07-15 11:08:20,065 - utils.logger - ERROR - Erreur lors de la migration: (pymysql.err.OperationalError) (1045, "Access denied for user 'root'@'localhost' (using password: YES)")
(Background on this error at: https://sqlalche.me/e/20/e3q8)
2025-07-15 11:08:20,066 - utils.logger - ERROR - Opération échouée

""")