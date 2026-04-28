import joblib
import numpy as np
import argparse
import os

BASE_DIR = os.path.dirname(os.path.abspath(__file__)) + '/'

# ─── Arguments en ligne de commande ──────────────────────────
# Usage   : python predict_cluster.py --haut_tot 12.5 --haut_tronc 3.2 --tronc_diam 0.45 --k 3
# ─────────────────────────────────────────────────────────────
parser = argparse.ArgumentParser(description='Prédit la catégorie de taille d\'un arbre.')
parser.add_argument('--haut_tot',   type=float, required=True, help='Hauteur totale en mètres (ex: 12.5)')
parser.add_argument('--haut_tronc', type=float, required=True, help='Hauteur du tronc en mètres (ex: 3.2)')
parser.add_argument('--tronc_diam', type=float, required=True, help='Diamètre du tronc en mètres (ex: 0.45)')
parser.add_argument('--k',          type=int,   required=True, help='Nombre de clusters : 2 ou 3')
args = parser.parse_args()

# Validation de k
if args.k not in [2, 3]:
    raise ValueError(f"[ERREUR] k doit être 2 ou 3 (reçu : {args.k})")

# Validation des mesures
for name, val in [('haut_tot', args.haut_tot), ('haut_tronc', args.haut_tronc), ('tronc_diam', args.tronc_diam)]:
    if val <= 0:
        raise ValueError(f"[ERREUR] {name} doit être strictement positif (reçu : {val})")

# Chargement du modèle (pas de réentraînement)
data      = joblib.load(BASE_DIR + f'clustering_k{args.k}.pkl')
scaler    = data['scaler']
model     = data['model']
label_map = data['label_map']

# Prédiction
X_scaled   = scaler.transform(np.array([[args.haut_tot, args.haut_tronc, args.tronc_diam]]))
cluster_id = int(model.predict(X_scaled)[0])
label      = label_map.get(cluster_id, f'Cluster {cluster_id}')

# Affichage
print("\n" + "=" * 40)
print(f"  RÉSULTAT DE PRÉDICTION (k={args.k})")
print("=" * 40)
print(f"  Hauteur totale  : {args.haut_tot} m")
print(f"  Hauteur tronc   : {args.haut_tronc} m")
print(f"  Diamètre tronc  : {args.tronc_diam} m")
print("-" * 40)
print(f"  Cluster         : {cluster_id}")
print(f"  Catégorie       : {label}")
print("=" * 40 + "\n")
