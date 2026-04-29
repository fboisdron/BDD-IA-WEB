-- MySQL version: mysql -u root -p saint_quentin_arbre < webapp/sql/schema_mysql.sql
-- Create database if needed:
-- CREATE DATABASE IF NOT EXISTS saint_quentin_arbre CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE saint_quentin_arbre;

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
    
    -- CHECK constraints
    CONSTRAINT chk_arbres_fk_stadedev CHECK (fk_stadedev IS NULL OR fk_stadedev IN ('adulte', 'jeune', 'senescent', 'vieux', 'N/A')),
    CONSTRAINT chk_arbres_fk_port CHECK (fk_port IS NULL OR fk_port IN ('architecturé', 'couronné', 'cépée', 'libre', 'rideau', 'réduit', 'réduit relâché', 'semi libre', 'têtard', 'têtard relâché', 'tête de chat', 'tête de chat relâché', 'étêté', 'N/A')),
    CONSTRAINT chk_arbres_fk_pied CHECK (fk_pied IS NULL OR fk_pied IN ('Bac de plantation', 'Revetement non permeable', 'bande de terre', 'fosse arbre', 'gazon', 'terre', 'toile tissée', 'végétation', 'N/A')),
    CONSTRAINT chk_arbres_fk_situation CHECK (fk_situation IS NULL OR fk_situation IN ('Alignement', 'Groupe', 'Isolé', 'N/A')),
    CONSTRAINT chk_arbres_fk_revetement CHECK (fk_revetement IS NULL OR fk_revetement IN ('Non', 'Oui', 'N/A')),
    CONSTRAINT chk_arbres_feuillage CHECK (feuillage IS NULL OR feuillage IN ('Conifère', 'Feuillu', 'N/A'))
);

CREATE INDEX idx_arbres_quartier ON arbres (clc_quartier(100));
CREATE INDEX idx_arbres_secteur ON arbres (clc_secteur(100));
CREATE INDEX idx_arbres_stade ON arbres (fk_stadedev(50));
CREATE INDEX idx_arbres_remarquable ON arbres (remarquable);

CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    username TEXT NOT NULL,
    email TEXT,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user' CHECK (role IN ('admin', 'user')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_username (username(255)),
    UNIQUE KEY unique_email (email(255))
);
