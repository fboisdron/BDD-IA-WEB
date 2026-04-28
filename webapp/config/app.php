<?php

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('APP_NAME', 'Saint-Quentin Arboré');
define('APP_BASE_URL', getenv('APP_BASE_URL') ?: '');

define('DB_HOST', getenv('PGHOST') ?: '127.0.0.1');
define('DB_PORT', getenv('PGPORT') ?: '5432');
define('DB_NAME', getenv('PGDATABASE') ?: 'saint_quentin_arbre');
define('DB_USER', getenv('PGUSER') ?: 'postgres');
define('DB_PASSWORD', getenv('PGPASSWORD') ?: 'postgres');

define('CSV_CLEAN_FILE', dirname(APP_ROOT) . '/BigData/data/Patrimoine_Arboré_data_clean.csv');
define('PYTHON_BIN', getenv('PYTHON_BIN') ?: 'python3');

define('TREE_INSERT_COLUMNS', [
    'clc_quartier', 'clc_secteur', 'id_arbre', 'haut_tot', 'haut_tronc', 'tronc_diam',
    'fk_arb_etat', 'fk_stadedev', 'fk_port', 'fk_pied', 'fk_situation', 'fk_revetement',
    'age_estim', 'fk_prec_estim', 'clc_nbr_diag', 'fk_nomtech', 'villeca', 'nomfrancais',
    'nomlatin', 'feuillage', 'remarquable', 'longitude', 'latitude'
]);
