import argparse
import pandas as pd
import joblib
import sys
from pathlib import Path


def charger_modele(model_path: str):
    resolved_path = Path(model_path)
    if not resolved_path.is_absolute():
        resolved_path = Path(__file__).resolve().parent / resolved_path

    try:
        return joblib.load(resolved_path)
    except FileNotFoundError:
        print(f"Erreur : modèle introuvable : {resolved_path}")
        sys.exit(1)
    except Exception as e:
        print(f"Erreur lors du chargement du modèle : {e}")
        sys.exit(1)


def construire_dataframe(args) -> pd.DataFrame:
    data = {
        "haut_tot": [args.haut_tot],
        "haut_tronc": [args.haut_tronc],
        "tronc_diam": [args.tronc_diam],
        "age_estim": [args.age_estim],
        "fk_stadedev": [args.fk_stadedev.strip().lower()],
        "fk_port": [args.fk_port.strip().lower()],
        "fk_pied": [args.fk_pied.strip().lower()],
        "fk_situation": [args.fk_situation.strip().lower()],
        "fk_revetement": [args.fk_revetement.strip().lower()],
        "feuillage": [args.feuillage.strip().lower()],
    }
    return pd.DataFrame(data)


def main():
    parser = argparse.ArgumentParser(
        description="Prédire le risque de déracinement d'un arbre lors d'une tempête."
    )

    # Arguments pour les caractéristiques de l'arbre
    parser.add_argument(
        "--model",
        default="random_forest_alerte.pkl",
        help="Chemin vers le fichier .pkl du modèle"
    )
    parser.add_argument("--haut_tot", type=float, required=True, help="Hauteur totale de l'arbre")
    parser.add_argument("--haut_tronc", type=float, required=True, help="Hauteur du tronc")
    parser.add_argument("--tronc_diam", type=float, required=True, help="Diamètre du tronc")
    parser.add_argument("--age_estim", type=float, required=True, help="Âge estimé de l'arbre")

    parser.add_argument("--fk_stadedev", type=str, required=True, help="Stade de développement")
    parser.add_argument("--fk_port", type=str, required=True, help="Port de l'arbre")
    parser.add_argument("--fk_pied", type=str, required=True, help="Type de pied")
    parser.add_argument("--fk_situation", type=str, required=True, help="Situation de l'arbre")
    parser.add_argument("--fk_revetement", type=str, required=True, help="Revêtement")
    parser.add_argument("--feuillage", type=str, required=True, help="Type de feuillage")

    args = parser.parse_args()

    # Charger le modèle et construire le DataFrame de test
    model = charger_modele(args.model)
    df_test = construire_dataframe(args)

    try:
        # Predire le risque de déracinement et la probabilité associée
        prediction = model.predict(df_test)[0]
        proba = model.predict_proba(df_test)[0][1]
    except Exception as e:
        print(f"Erreur pendant la prédiction : {e}")
        sys.exit(1)

    print("=== Résultat de la prédiction ===")
    print(df_test.to_string(index=False))
    print()

    if prediction == 1:
        print("ALERTE : cet arbre est considéré comme susceptible d'être déraciné en cas de tempête.")
    else:
        print("PAS D'ALERTE : cet arbre est considéré comme peu susceptible d'être déraciné en cas de tempête.")

    print(f"Probabilité estimée de risque : {proba:.4f}")


if __name__ == "__main__":
    main()