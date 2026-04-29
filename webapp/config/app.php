<?php

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('APP_NAME', 'Saint-Quentin Arboré');
define('APP_BASE_URL', getenv('APP_BASE_URL') ?: '');

// Database type: 'pgsql' for PostgreSQL, 'mysql' for MySQL
$dbType = getenv('DB_TYPE') ?: 'pgsql';
define('DB_TYPE', $dbType);

if ($dbType === 'mysql') {
    define('DB_HOST', getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: '127.0.0.1');
    define('DB_PORT', getenv('MYSQL_PORT') ?: getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'saint_quentin_arbre');
    define('DB_USER', getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root');
    define('DB_PASSWORD', getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: '');
} else {
    // PostgreSQL (legacy)
    define('DB_HOST', getenv('PGHOST') ?: getenv('DB_HOST') ?: '127.0.0.1');
    define('DB_PORT', getenv('PGPORT') ?: getenv('DB_PORT') ?: '5432');
    define('DB_NAME', getenv('PGDATABASE') ?: getenv('DB_NAME') ?: 'saint_quentin_arbre');
    define('DB_USER', getenv('PGUSER') ?: getenv('DB_USER') ?: 'postgres');
    define('DB_PASSWORD', getenv('PGPASSWORD') ?: getenv('DB_PASSWORD') ?: 'postgres');
}

define('CSV_CLEAN_FILE', dirname(APP_ROOT) . '/BigData/data/Patrimoine_Arboré_data_clean.csv');
$defaultPythonBin = dirname(APP_ROOT) . '/BigData/nettoyage/.venv/bin/python';
define('PYTHON_BIN', getenv('PYTHON_BIN') ?: (is_file($defaultPythonBin) ? $defaultPythonBin : 'python3'));

define('TREE_INSERT_COLUMNS', [
    'clc_quartier', 'clc_secteur', 'id_arbre', 'haut_tot', 'haut_tronc', 'tronc_diam',
    'fk_arb_etat', 'fk_stadedev', 'fk_port', 'fk_pied', 'fk_situation', 'fk_revetement',
    'age_estim', 'fk_prec_estim', 'clc_nbr_diag', 'fk_nomtech', 'villeca', 'nomfrancais',
    'nomlatin', 'feuillage', 'remarquable', 'longitude', 'latitude'
]);
