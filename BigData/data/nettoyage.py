import pandas as pd
import numpy as np
from pyproj import Transformer

INPUT_FILE = '../data/Patrimoine_Arboré_data.csv'   # à adapter si besoin
OUTPUT_FILE = '../data/Patrimoine_Arboré_data_clean.csv'
RAPPORT_FILE = '../data/rapport_nettoyage.txt'

# Date de référence pour calculer un âge à partir de dte_plantation
# Choix prudent : prendre la date la plus récente trouvée dans les colonnes de suivi,
# pour rester cohérent avec le jeu de données et éviter d'utiliser la date du jour.
REFERENCE_DATE = pd.Timestamp('2018-01-15', tz='UTC')


def convert_lambert93_to_wgs84(df: pd.DataFrame) -> pd.DataFrame:
    """Convertit les coordonnées Lambert93 (X, Y) en WGS84 (Latitude, Longitude)."""
    df = df.copy()
    
    if 'X' in df.columns and 'Y' in df.columns:
        # Transformer Lambert93 (EPSG:2154) -> WGS84 (EPSG:4326)
        transformer = Transformer.from_crs("EPSG:2154", "EPSG:4326", always_xy=True)
        
        # Convertir les colonnes X et Y en numériques (ignorer les erreurs)
        df['X'] = pd.to_numeric(df['X'], errors='coerce')
        df['Y'] = pd.to_numeric(df['Y'], errors='coerce')
        
        # Appliquer la transformation
        lons, lats = transformer.transform(df['X'].values, df['Y'].values)
        
        # Remplacer X par Longitude et Y par Latitude
        df['Longitude'] = lons
        df['Latitude'] = lats
        
        # Supprimer les anciennes colonnes X et Y
        df = df.drop(columns=['X', 'Y'])
        
        print("✓ Conversion Lambert93 → WGS84 effectuée")
        print(f"  Exemples :")
        print(df[['Latitude', 'Longitude']].head())
    
    return df

def normalize_missing_values(df: pd.DataFrame) -> pd.DataFrame:
    """Uniformise les valeurs vides en NaN."""
    df = df.copy()

    # Remplace les chaînes vides / espaces / placeholders courants par NaN
    for col in df.columns:
        if df[col].dtype == 'object':
            df[col] = df[col].replace(r'^\s*$', np.nan, regex=True)
            df[col] = df[col].replace({'(Blank)': np.nan, 'N/A': np.nan, 'n/a': np.nan})

    return df


def standardize_text_values(df: pd.DataFrame) -> pd.DataFrame:
    """Nettoie les libellés textuels incohérents."""
    df = df.copy()

    # Harmonisation du port
    if 'fk_port' in df.columns:
        df['fk_port'] = df['fk_port'].replace({
            'Libre': 'libre',
            'Semi libre': 'semi libre',
            'Couronne': 'couronné',
            'courroné': 'couronné',
            'couronné': 'couronné',
            'tête de chat relaché': 'tête de chat relâché',
        })

    # Harmonisation du stade de développement
    if 'fk_stadedev' in df.columns:
        df['fk_stadedev'] = df['fk_stadedev'].replace({
            'Jeune': 'jeune',
            'Adulte': 'adulte',
        })

    # Harmonisation du pied
    if 'fk_pied' in df.columns:
        df['fk_pied'] = df['fk_pied'].replace({
            'Terre': 'terre',
            'Bande de terre': 'bande de terre',
        })

    # Harmonisation de src_geo (si on souhaite l'analyser avant suppression)
    if 'src_geo' in df.columns:
        df['src_geo'] = df['src_geo'].replace({
            'orthophoto': 'Orthophoto',
            'Orthophoto plan': 'Orthophoto',
            'Plan ortho': 'Orthophoto',
            'à renseigner': np.nan,
        })

    return df


def clean_target_column(df: pd.DataFrame) -> pd.DataFrame:
    """Crée une cible binaire optionnelle à partir de fk_arb_etat."""
    df = df.copy()

    if 'fk_arb_etat' in df.columns:
        df['fk_arb_etat'] = df['fk_arb_etat'].replace({
            'SUPPRIMÉ': 'ABATTU',
            'REMPLACÉ': 'ABATTU',
            'Essouché': 'ABATTU',
            'Non essouché': 'ABATTU',
        })

        # # Cible binaire pour la ML
        # mapping = {
        #     'EN PLACE': 0,
        #     'ABATTU': 1,
        # }
        # df['cible_abattage'] = df['fk_arb_etat'].map(mapping)

    return df




def fill_age_from_planting_date(df: pd.DataFrame) -> pd.DataFrame:
    """Complète age_estim uniquement quand il manque ET que dte_plantation existe."""
    df = df.copy()

    if 'age_estim' in df.columns and 'dte_plantation' in df.columns:
        print("\n=== DEBUG age_estim / dte_plantation ===")

        print("\nValeurs brutes dte_plantation :")
        print(df['dte_plantation'].head(10))

        # Conversion explicite en datetime
        df['dte_plantation'] = pd.to_datetime(df['dte_plantation'], errors='coerce', utc=True)

        print("\nValeurs converties dte_plantation :")
        print(df['dte_plantation'].head(10))

        # Conversion age_estim en numérique
        df['age_estim'] = pd.to_numeric(df['age_estim'], errors='coerce')

        age_calc = ((REFERENCE_DATE - df['dte_plantation']).dt.days / 365.25).round()

        print("\nÂges calculés :")
        print(age_calc.head(10))

        mask = (
            df['age_estim'].isna()
            & df['dte_plantation'].notna()
            & age_calc.notna()
            & (age_calc >= 0)
        )

        print("\nNombre de lignes où age_estim va être rempli :", mask.sum())

        print("\nExemples de lignes remplies :")
        debug_df = pd.DataFrame({
            'dte_plantation': df['dte_plantation'],
            'age_estim_avant': df['age_estim'],
            'age_calc': age_calc,
            'sera_rempli': mask
        })
        print(debug_df[debug_df['sera_rempli']].head(20))

        df.loc[mask, 'age_estim'] = age_calc.loc[mask]

        print("\nExemples après remplissage :")
        print(df.loc[mask, ['dte_plantation', 'age_estim']].head(20))

    return df


def replace_zero_with_nan(df: pd.DataFrame, cols: list[str]) -> pd.DataFrame:
    """Remplace les 0 par NaN dans des colonnes où 0 est peu crédible physiquement."""
    df = df.copy()
    for col in cols:
        if col in df.columns:
            df[col] = pd.to_numeric(df[col], errors='coerce')
            df.loc[df[col] == 0, col] = np.nan
    return df


def fill_categorical_na(df: pd.DataFrame, cols: list[str], fill_value='N/A') -> pd.DataFrame:
    """Remplace les valeurs manquantes de colonnes qualitatives par une catégorie explicite."""
    df = df.copy()
    for col in cols:
        if col in df.columns:
            df[col] = df[col].fillna(fill_value)
    return df


def drop_columns(df: pd.DataFrame) -> pd.DataFrame:
    """Supprime les colonnes inutiles ou à forte fuite d'information."""
    df = df.copy()

    cols_to_drop = [
        'Editor', 'EditDate', 'Creator', 'CreationDate',
        'GlobalID', 'OBJECTID',
        'last_edited_date', 'last_edited_user',
        'created_date', 'created_user',
        'commentaire_environnement', 'src_geo',
        'dte_abattage',
    ]

    # dte_plantation : on la garde assez longtemps pour compléter age_estim,
    # puis on la supprime pour éviter une colonne très incomplète dans le modèle.
    if 'dte_plantation' in df.columns:
        cols_to_drop.append('dte_plantation')

    existing = [c for c in cols_to_drop if c in df.columns]
    return df.drop(columns=existing)


def generate_report(before_df: pd.DataFrame, after_df: pd.DataFrame, path: str) -> None:
    lines = []
    lines.append('RAPPORT DE NETTOYAGE\n')
    lines.append(f'Lignes avant nettoyage : {before_df.shape[0]}\n')
    lines.append(f'Colonnes avant nettoyage : {before_df.shape[1]}\n')
    lines.append(f'Lignes après nettoyage : {after_df.shape[0]}\n')
    lines.append(f'Colonnes après nettoyage : {after_df.shape[1]}\n\n')

    lines.append('Valeurs manquantes restantes par colonne :\n')
    missing = after_df.isna().sum().sort_values(ascending=False)
    for col, n in missing.items():
        if n > 0:
            lines.append(f' - {col}: {n}\n')

    lines.append('\nDoublons exacts restants : ')
    lines.append(str(after_df.duplicated().sum()))
    lines.append('\n')

    with open(path, 'w', encoding='utf-8') as f:
        f.writelines(lines)


def main():
    df = pd.read_csv(INPUT_FILE, low_memory=False)
    df_before = df.copy()

    # 0) Convertir les coordonnées Lambert93 en WGS84
    df = convert_lambert93_to_wgs84(df)

    # 1) Uniformiser les NA
    df = normalize_missing_values(df)

    # 2) Standardiser les libellés
    df = standardize_text_values(df)

    # 3) Nettoyer la cible / créer une cible binaire
    df = clean_target_column(df)

    # 4) Colonnes numériques : 0 => NaN si valeur non crédible
    df = replace_zero_with_nan(df, ['haut_tot', 'haut_tronc', 'tronc_diam'])

    # 5) Compléter l'âge via la date de plantation quand possible
    df = fill_age_from_planting_date(df)

    # 6) Remplissement des NA sur variables qualitatives
    categorical_na_cols = [
        'remarquable', 'feuillage', 'nomlatin', 'nomfrancais', 'villeca',
        'fk_nomtech', 'fk_revetement', 'fk_situation', 'fk_pied',
        'fk_stadedev', 'fk_port', 'fk_arb_etat'
    ]
    df = fill_categorical_na(df, categorical_na_cols, fill_value='N/A')

    # 7) fk_prec_estim : garder la logique métier simple
    # - si age_estim est manquant => précision sans sens -> N/A
    # - sinon si fk_prec_estim manque => 0 (précision inconnue/non renseignée)
    if 'fk_prec_estim' in df.columns:
        df['fk_prec_estim'] = pd.to_numeric(df['fk_prec_estim'], errors='coerce')
        mask_age_missing = df['age_estim'].isna()
        df.loc[mask_age_missing, 'fk_prec_estim'] = np.nan
        df['fk_prec_estim'] = df['fk_prec_estim'].fillna(0)

    # 8) clc_nbr_diag : garder la colonne, sans imputation agressive par défaut
    if 'clc_nbr_diag' in df.columns:
        df['clc_nbr_diag'] = pd.to_numeric(df['clc_nbr_diag'], errors='coerce')

    # 9) Supprimer les colonnes inutiles
    df = drop_columns(df)

    # 10) Supprimer les doublons exacts de lignes si présents
    df = df.drop_duplicates().reset_index(drop=True)

    # 11) Sauvegardes
    df.to_csv(OUTPUT_FILE, index=False, encoding='utf-8-sig')
    df.to_excel(OUTPUT_FILE.replace('.csv', '.xlsx'), index=False)
    generate_report(df_before, df, RAPPORT_FILE)

    print('Nettoyage terminé.')
    print(f'Fichier nettoyé : {OUTPUT_FILE}')
    print(f'Rapport : {RAPPORT_FILE}')


if __name__ == '__main__':
    main()
