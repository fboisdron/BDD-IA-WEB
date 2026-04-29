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

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint WHERE conname = 'chk_arbres_fk_stadedev'
    ) THEN
        ALTER TABLE arbres
            ADD CONSTRAINT chk_arbres_fk_stadedev
            CHECK (fk_stadedev IS NULL OR fk_stadedev IN ('adulte', 'jeune', 'senescent', 'vieux', 'N/A'));
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint WHERE conname = 'chk_arbres_fk_port'
    ) THEN
        ALTER TABLE arbres
            ADD CONSTRAINT chk_arbres_fk_port
            CHECK (
                fk_port IS NULL OR fk_port IN (
                    'architecturé', 'couronné', 'cépée', 'libre', 'rideau', 'réduit',
                    'réduit relâché', 'semi libre', 'têtard', 'têtard relâché',
                    'tête de chat', 'tête de chat relâché', 'étêté', 'N/A'
                )
            );
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint WHERE conname = 'chk_arbres_fk_pied'
    ) THEN
        ALTER TABLE arbres
            ADD CONSTRAINT chk_arbres_fk_pied
            CHECK (
                fk_pied IS NULL OR fk_pied IN (
                    'Bac de plantation', 'Revetement non permeable', 'bande de terre',
                    'fosse arbre', 'gazon', 'terre', 'toile tissée', 'végétation', 'N/A'
                )
            );
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint WHERE conname = 'chk_arbres_fk_situation'
    ) THEN
        ALTER TABLE arbres
            ADD CONSTRAINT chk_arbres_fk_situation
            CHECK (fk_situation IS NULL OR fk_situation IN ('Alignement', 'Groupe', 'Isolé', 'N/A'));
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint WHERE conname = 'chk_arbres_fk_revetement'
    ) THEN
        ALTER TABLE arbres
            ADD CONSTRAINT chk_arbres_fk_revetement
            CHECK (fk_revetement IS NULL OR fk_revetement IN ('Non', 'Oui', 'N/A'));
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint WHERE conname = 'chk_arbres_feuillage'
    ) THEN
        ALTER TABLE arbres
            ADD CONSTRAINT chk_arbres_feuillage
            CHECK (feuillage IS NULL OR feuillage IN ('Conifère', 'Feuillu', 'N/A'));
    END IF;
END $$;
