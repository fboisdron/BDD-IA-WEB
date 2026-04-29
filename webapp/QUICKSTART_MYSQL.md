# Démarrage rapide avec MySQL

## Installation minimale (5 min)

### 1. Créer la base de données MySQL

```bash
mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS saint_quentin_arbre CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF
```

### 2. Définir l'environnement

```bash
# Depuis le répertoire racine /home/isen/BDD-IA-WEB/
export DB_TYPE=mysql
export MYSQL_HOST=127.0.0.1
export MYSQL_PORT=3306
export MYSQL_DATABASE=saint_quentin_arbre
export MYSQL_USER=root
export MYSQL_PASSWORD=  # Laisser vide si pas de password
```

### 3. Importer les données

```bash
php webapp/scripts/import_csv.php BigData/data/Patrimoine_Arboré_data_clean.csv
```

### 4. Tester la connexion

```bash
php webapp/scripts/test_db.php
```

### 5. Lancer le serveur web

```bash
php -S 127.0.0.1:8080 -t webapp/public
```

Puis ouvrir `http://127.0.0.1:8080` dans le navigateur.

## Fichiers modifiés pour MySQL

| Fichier | Changement |
|---------|-----------|
| `config/app.php` | Support `DB_TYPE` (mysql\|pgsql), variables MYSQL_* |
| `lib/Database.php` | DSN MySQL + PDO MySQL |
| `sql/schema_mysql.sql` | Schéma compatible MySQL (AUTOINCREMENT, DECIMAL, etc.) |
| `scripts/import_csv.php` | Détection automatique du schéma selon `DB_TYPE` |

## Revenir à PostgreSQL (si besoin)

```bash
export DB_TYPE=pgsql
export PGHOST=127.0.0.1
export PGPORT=5432
export PGDATABASE=saint_quentin_arbre
export PGUSER=postgres
export PGPASSWORD=postgres
```

## Fichiers de référence

- **[MIGRATION_MYSQL.md](MIGRATION_MYSQL.md)** - Guide complet de migration
- **[sql/README.md](sql/README.md)** - Différences schéma PostgreSQL/MySQL
- **[.env.example](.env.example)** - Variables d'environnement
- **[README.md](README.md)** - Documentation générale
