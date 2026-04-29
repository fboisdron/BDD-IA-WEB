# Saint-Quentin Arboré

Application web PHP/HTML/CSS/JS reliée à PostgreSQL ou MySQL et aux scripts Python du projet BigData/IA.

## Arborescence

- `public/` : pages front, API AJAX, assets
- `config/` : paramètres de connexion et constantes
- `lib/` : accès base de données et pont Python
- `sql/` : schémas PostgreSQL et MySQL
- `scripts/` : utilitaires CLI (import CSV, test DB)

## Prérequis

### PHP
- PHP 8.1+ avec extension `pdo`
- Pour PostgreSQL : `php-pgsql` 
- Pour MySQL : `php-mysql`
- Python 3 (pour les scripts IA appelés par l'API)

### Base de données (choisir une option)
- **Option 1 (défaut)** : PostgreSQL 13+
- **Option 2 (nouveau)** : MySQL 8.0+ 

Vérifier rapidement :

```bash
php -v
php -m | grep -E 'pdo|pgsql|mysql'
python3 --version

# PostgreSQL (optionnel)
psql --version

# MySQL (optionnel)
mysql --version
```

## Configuration de la base de données

Voir [MIGRATION_MYSQL.md](MIGRATION_MYSQL.md) pour **passer de PostgreSQL à MySQL**.

### Initialiser MySQL

Voir [MIGRATION_MYSQL.md](MIGRATION_MYSQL.md) pour les étapes d'installation et de migration depuis PostgreSQL.

**Résumé rapide :**

```bash
# 1. Créer la base de données
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS saint_quentin_arbre CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Définir les variables d'environnement
export DB_TYPE=mysql
export MYSQL_HOST=127.0.0.1
export MYSQL_PORT=3306
export MYSQL_DATABASE=saint_quentin_arbre
export MYSQL_USER=root
export MYSQL_PASSWORD=

# 3. Charger le schéma et importer les données
php webapp/scripts/import_csv.php BigData/data/Patrimoine_Arboré_data_clean.csv

# 4. Tester la connexion
php webapp/scripts/test_db.php
```

### Initialiser PostgreSQL (par défaut)

### 1) Créer la base et l’utilisateur

Exemple (à adapter si vous avez déjà un user/base) :

```bash
sudo -u postgres psql
```

Puis dans `psql` :

```sql
CREATE USER sq_web_user WITH PASSWORD 'sq_web_password';
CREATE DATABASE saint_quentin_arbre OWNER sq_web_user;
GRANT ALL PRIVILEGES ON DATABASE saint_quentin_arbre TO sq_web_user;
\q
```

### 2) Renseigner les variables d’environnement

Depuis la racine du repo (`/home/isen/BDD-IA-WEB`) :

```bash
export PGHOST=127.0.0.1
export PGPORT=5432
export PGDATABASE=saint_quentin_arbre
export PGUSER=postgres
export PGPASSWORD=postgres
```

Note : ces variables sont lues par `webapp/config/app.php`.

### 3) Créer les tables + importer le CSV clean

Option recommandée depuis la racine du repo :

```bash
php webapp/scripts/import_csv.php BigData/data/Patrimoine_Arboré_data_clean.csv
```

Option si vous êtes déjà dans `webapp/` :

```bash
php scripts/import_csv.php ../BigData/data/Patrimoine_Arboré_data_clean.csv
```

Le script :

- applique automatiquement `webapp/sql/schema.sql`,
- puis insère les lignes du CSV.

### 4) Vérifier que l’import est correct

```bash
psql -h "$PGHOST" -p "$PGPORT" -U "$PGUSER" -d "$PGDATABASE" -c "SELECT COUNT(*) FROM arbres;"
```

## Lancer le serveur web local

### Option A : serveur intégré PHP (développement local)

Depuis la racine du repo :

```bash
php -S 127.0.0.1:8080 -t webapp/public
```

Puis ouvrir :

- `http://127.0.0.1:8080/index.php`
- `http://127.0.0.1:8080/cartes.php`
- `http://127.0.0.1:8080/besoins.php`

### Option B : Apache/Nginx

Configurer le virtual host pour que la racine web pointe vers `webapp/public`.

## Tester rapidement l’API AJAX

Depuis un autre terminal :

```bash
curl "http://127.0.0.1:8080/api.php?action=summary"
curl "http://127.0.0.1:8080/api.php?action=trees&limit=5"
curl "http://127.0.0.1:8080/api.php?action=map&mode=age"
```

## Scripts Python appelés par l’API

Les formulaires "Besoins clients" appellent ces scripts :

- `IA/2-modele-prediction-age/script.py`
- `IA/1 - Visualisation-carte/predict_cluster.py`
- `IA/3-Systeme-alerte-tempête/predire_alerte.py`

Si besoin, définir un interpréteur Python spécifique :

```bash
export PYTHON_BIN=/chemin/vers/python3
```

## Dépannage

- `Exit code 1` pendant l’import : vérifier le chemin CSV et le répertoire courant.
	Exemple : depuis `webapp/`, utiliser `php scripts/import_csv.php ../BigData/data/Patrimoine_Arboré_data_clean.csv`.
- `Connexion PostgreSQL/MySQL indisponible` : vérifier les variables d'environnement `DB_TYPE`, `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` ou `PGHOST`, `PGPORT`, etc.
- `could not find driver` : extension `pdo_pgsql` ou `pdo_mysql` absente. Installer avec `apt-get install php-pgsql` ou `apt-get install php-mysql`.
- Erreur sur un script Python depuis `api.php` : vérifier les dépendances Python et les modèles `.pkl` attendus.
- Utiliser `php webapp/scripts/test_db.php` pour tester rapidement la connexion à la base de données.

## Fonctionnalités couvertes

- Header / Footer communs
- Page d’accueil
- Page d’ajout d’arbres
- Pages de cartes pour les besoins clients
- Formulaires AJAX connectés aux scripts Python existants
