"""
Besoin client 2 – Script final : prédiction de l'âge d'un arbre.

Approche : régression (Gradient Boosting) sur age_estim (continu).

Usage :
    python script.py --haut_tot <m> --haut_tronc <m> --tronc_diam <m>
                     --clc_nbr_diag <n>
                     --remarquable <Oui|Non>

Exemple :
    python3 script.py --haut_tot 8.0 --haut_tronc 2.5 --tronc_diam 0.6 --clc_nbr_diag 0 --remarquable Non

Le modèle doit avoir été entraîné via notebook1.ipynb (models/reg_pipeline.pkl).
"""

import argparse
import sys
import os
import pandas as pd
import joblib

MODELS_DIR    = os.path.join(os.path.dirname(__file__), 'models')
PIPELINE_PATH = os.path.join(MODELS_DIR, 'reg_pipeline.pkl')


def load_model():
    if not os.path.exists(PIPELINE_PATH):
        print(f"Erreur : fichier introuvable : {PIPELINE_PATH}")
        print("Veuillez exécuter notebook1.ipynb pour générer le modèle.")
        sys.exit(1)
    return joblib.load(PIPELINE_PATH)


def predict(haut_tot, haut_tronc, tronc_diam, clc_nbr_diag, remarquable):
    pipeline = load_model()

    X = pd.DataFrame([{
        'haut_tot'    : haut_tot,
        'haut_tronc'  : haut_tronc,
        'tronc_diam'  : tronc_diam,
        'clc_nbr_diag': clc_nbr_diag,
        'remarquable' : remarquable,
    }])

    age_predit = pipeline.predict(X)[0]
    return max(0, age_predit)


def main():
    parser = argparse.ArgumentParser(description="Prédit l'âge estimé d'un arbre en années.")
    parser.add_argument('--haut_tot',     type=float, required=True)
    parser.add_argument('--haut_tronc',   type=float, required=True)
    parser.add_argument('--tronc_diam',   type=float, required=True)
    parser.add_argument('--clc_nbr_diag', type=float, default=0)
    parser.add_argument('--remarquable',  type=str,   default='Non',
                        choices=['Oui', 'Non'])
    args = parser.parse_args()

    age_predit = predict(
        args.haut_tot, args.haut_tronc, args.tronc_diam,
        args.clc_nbr_diag, args.remarquable
    )

    print("\n--- Résultat ---")
    print(f"  Hauteur totale  : {args.haut_tot} m")
    print(f"  Hauteur tronc   : {args.haut_tronc} m")
    print(f"  Diamètre tronc  : {args.tronc_diam} m")
    print(f"  Nb diagnostics  : {args.clc_nbr_diag}")
    print(f"  Remarquable     : {args.remarquable}")
    print(f"  Âge estimé      : {age_predit:.1f} ans")


if __name__ == '__main__':
    main()
