#!/bin/bash
# Script pour configurer rapidement MySQL pour l'application

set -e

echo "=========================================="
echo "Configuration MySQL - Saint-Quentin Arboré"
echo "=========================================="
echo ""

# Defaults
DB_HOST="${MYSQL_HOST:-127.0.0.1}"
DB_PORT="${MYSQL_PORT:-3306}"
DB_NAME="${MYSQL_DATABASE:-saint_quentin_arbre}"
DB_USER="${MYSQL_USER:-root}"
CSV_PATH="${1:-BigData/data/Patrimoine_Arboré_data_clean.csv}"

echo "Configuration:"
echo "  Host: $DB_HOST"
echo "  Port: $DB_PORT"
echo "  Database: $DB_NAME"
echo "  User: $DB_USER"
echo "  CSV: $CSV_PATH"
echo ""

# Check if CSV exists
if [ ! -f "$CSV_PATH" ]; then
    echo "❌ Erreur: CSV non trouvé à $CSV_PATH"
    exit 1
fi

# Check if mysql command exists
if ! command -v mysql &> /dev/null; then
    echo "❌ Erreur: mysql CLI non trouvé. Installez mysql-client."
    exit 1
fi

# Check if PHP exists
if ! command -v php &> /dev/null; then
    echo "❌ Erreur: PHP non trouvé."
    exit 1
fi

# Prompt for MySQL password
read -sp "Mot de passe MySQL (root): " DB_PASSWORD
echo ""

# Test MySQL connection
echo -n "Vérification de la connexion MySQL... "
if ! mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1" &> /dev/null; then
    echo "❌"
    echo "Erreur: Impossible de se connecter à MySQL"
    exit 1
fi
echo "✓"

# Create database
echo -n "Création de la base de données... "
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" << EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF
echo "✓"

# Set environment variables
export DB_TYPE=mysql
export MYSQL_HOST="$DB_HOST"
export MYSQL_PORT="$DB_PORT"
export MYSQL_DATABASE="$DB_NAME"
export MYSQL_USER="$DB_USER"
export MYSQL_PASSWORD="$DB_PASSWORD"

# Import data and schema
echo "Import du CSV et création des tables..."
php webapp/scripts/import_csv.php "$CSV_PATH"

# Test connection
echo -n "Test de la connexion... "
php webapp/scripts/test_db.php > /dev/null 2>&1 && echo "✓" || echo "⚠ Attention: Le test a échoué"

echo ""
echo "=========================================="
echo "✓ Configuration terminée!"
echo "=========================================="
echo ""
echo "Prochaines étapes:"
echo "1. Lancer le serveur web:"
echo "   php -S 127.0.0.1:8080 -t webapp/public"
echo ""
echo "2. Ouvrir http://127.0.0.1:8080"
echo ""
echo "Note: Les variables d'environnement ci-dessous ont été définies pour cette session:"
echo "  export DB_TYPE=mysql"
echo "  export MYSQL_HOST=$DB_HOST"
echo "  export MYSQL_PORT=$DB_PORT"
echo "  export MYSQL_DATABASE=$DB_NAME"
echo "  export MYSQL_USER=$DB_USER"
echo ""
echo "Pour les rendre persistantes, ajouter ces lignes à votre ~/.bashrc ou ~/.zshrc"
