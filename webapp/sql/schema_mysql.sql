-- MySQL version: mysql -u f_boisdr -p f_boisdr < /var/www/f_boisdr/sql/schema_mysql.sql

CREATE TABLE IF NOT EXISTS arbres (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    clc_quartier TEXT,
    clc_secteur TEXT,
    id_arbre BIGINT,
    haut_tot DECIMAL(10, 2),
    haut_tronc DECIMAL(10, 2),
    tronc_diam DECIMAL(10, 4),
    fk_arb_etat INTEGER,
    fk_stadedev TEXT,
    fk_port TEXT,
    fk_pied TEXT,
    fk_situation TEXT,
    fk_revetement TEXT,
    age_estim DECIMAL(10, 2),
    fk_prec_estim INTEGER,
    clc_nbr_diag INTEGER,
    fk_nomtech TEXT,
    villeca TEXT,
    nomfrancais TEXT,
    nomlatin TEXT,
    feuillage TEXT,
    remarquable INTEGER DEFAULT 0,
    longitude DOUBLE,
    latitude DOUBLE,
    alerte_tempete BOOLEAN DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_arbres_quartier (clc_quartier(100)),
    INDEX idx_arbres_secteur (clc_secteur(100)),
    INDEX idx_arbres_stade (fk_stadedev(50)),
    INDEX idx_arbres_remarquable (remarquable)
);

CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_username (username),
    UNIQUE KEY unique_email (email)
);
