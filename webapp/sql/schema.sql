CREATE TABLE IF NOT EXISTS arbres (
    id BIGSERIAL PRIMARY KEY,
    clc_quartier TEXT,
    clc_secteur TEXT,
    id_arbre BIGINT,
    haut_tot NUMERIC(10, 2),
    haut_tronc NUMERIC(10, 2),
    tronc_diam NUMERIC(10, 4),
    fk_arb_etat INTEGER,
    fk_stadedev TEXT,
    fk_port TEXT,
    fk_pied TEXT,
    fk_situation TEXT,
    fk_revetement TEXT,
    age_estim NUMERIC(10, 2),
    fk_prec_estim INTEGER,
    clc_nbr_diag INTEGER,
    fk_nomtech TEXT,
    villeca TEXT,
    nomfrancais TEXT,
    nomlatin TEXT,
    feuillage TEXT,
    remarquable INTEGER DEFAULT 0,
    longitude DOUBLE PRECISION,
    latitude DOUBLE PRECISION,
    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_arbres_quartier ON arbres (clc_quartier);
CREATE INDEX IF NOT EXISTS idx_arbres_secteur ON arbres (clc_secteur);
CREATE INDEX IF NOT EXISTS idx_arbres_stade ON arbres (fk_stadedev);
CREATE INDEX IF NOT EXISTS idx_arbres_remarquable ON arbres (remarquable);
