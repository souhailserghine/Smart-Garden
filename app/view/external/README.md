# External Files & Utilities

Ce dossier contient tous les fichiers externes et utilitaires du projet SmartGarden.

## 📋 Fichiers inclus:

### Configuration
- `.env` - Variables d'environnement (Groq API, etc.)
- `.env.example` - Exemple de configuration

### Migrations de base de données
- `migrate_add_image_temp.php` - Ajoute les colonnes image et temperature à la table plante
- `migrate_add_temperature_to_suggestions.php` - Ajoute les colonnes temperature et autres à la table suggestionplante
- `add_temperature.php` - Script d'ajout de temperature
- `recreate_table.php` - Recréer les tables
- `execute_create_table.php` - Exécuter les créations de tables

### Scripts SQL
- `create_suggestion_table.sql` - Création de la table suggestionplante
- `create_suggestiontache_table.sql` - Création de la table suggestiontache
- `update_suggestion_table.sql` - Mise à jour de la table suggestion

### API & Utilitaires
- `chatgpt_api.php` - Integration API pour les suggestions (déprécié, utilise maintenant Groq)
- `config_env.php` - Configuration des variables d'environnement

### Tests
- `test_suggestiontache.php` - Tests du système de suggestions de tâches

## 🚀 Comment exécuter les migrations:

```bash
php migrate_add_image_temp.php
php migrate_add_temperature_to_suggestions.php
```

## ⚠️ Notes importantes:

- Ces fichiers ne sont pas indispensables au fonctionnement du projet en production
- Les migrations ont déjà été exécutées sur la base de données
- Gardez `.env` et `.env.example` à jour pour les configurations sensibles
