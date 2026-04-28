# Utiliser le script de clustering / visualisation cartographique

Ce dossier contient un script Python permettant de prédire la **catégorie de taille d'un arbre** selon ses dimensions (hauteur totale, hauteur du tronc, diamètre du tronc).

Le script utilise un modèle de clustering K-means entraîné pour classer les arbres en 2 ou 3 groupes de taille. Cela facilite la visualisation et l'analyse spatiale sur une carte.

Le script à exécuter est [predict_cluster.py](predict_cluster.py). Par défaut, il charge le modèle [clustering_k2.pkl](clustering_k2.pkl) ou [clustering_k3.pkl](clustering_k3.pkl) selon le paramètre `k` fourni.


## Créer et activer un environnement virtuel (venv)

Sur Linux, depuis le dossier `IA/1 - Visualisation-carte` :

```bash
# créer l'environnement
python3 -m venv .venv

# activer
source .venv/bin/activate

# mettre pip à jour
pip install --upgrade pip
```

Pour sortir de l'environnement virtuel, utilisez `deactivate`.

## Installer les dépendances

Une fois le `venv` activé, installez les paquets :

```bash
pip install -r requirements.txt
```

## Lancer une prédiction

Placez-vous dans le dossier `IA/1 - Visualisation-carte`, puis exécutez le script avec les dimensions de l'arbre et le nombre de clusters souhaité.

### Exemple avec k=2 (2 groupes de taille)

```bash
python predict_cluster.py \
  --haut_tot 12.5 \
  --haut_tronc 3.2 \
  --tronc_diam 0.45 \
  --k 2
```

**Résultat attendu :**
```
========================================
  RÉSULTAT DE PRÉDICTION (k=2)
========================================
  Hauteur totale  : 12.5 m
  Hauteur tronc   : 3.2 m
  Diamètre tronc  : 0.45 m
----------------------------------------
  Cluster         : 0
  Catégorie       : [Petit/Moyen selon le modèle]
========================================
```

### Exemple avec k=3 (3 groupes de taille)

```bash
python predict_cluster.py \
  --haut_tot 25.0 \
  --haut_tronc 8.0 \
  --tronc_diam 0.80 \
  --k 3
```

Le script affiche :
- Les dimensions de l'arbre (hauteur totale, hauteur du tronc, diamètre du tronc),
- Le **numéro du cluster** (groupe de taille auquel appartient l'arbre),
- La **catégorie** associée au cluster (dépend du modèle entraîné).

## Paramètres obligatoires

Tous les paramètres suivants doivent être fournis :

- `--haut_tot` : hauteur totale de l'arbre (en mètres, > 0),
- `--haut_tronc` : hauteur du tronc (en mètres, > 0),
- `--tronc_diam` : diamètre du tronc (en mètres, > 0),
- `--k` : nombre de clusters/groupes de taille (**2 ou 3 uniquement**).

## Modèles disponibles

Deux modèles de clustering pré-entraînés sont disponibles :

- **clustering_k2.pkl** : modèle K-means avec k=2 (2 groupes de taille)
- **clustering_k3.pkl** : modèle K-means avec k=3 (3 groupes de taille)

Choisissez le modèle via le paramètre `--k`.

## Utilisation dans une pipeline de visualisation cartographique

Pour visualiser les arbres sur une carte selon leur catégorie de taille :

1. Charger le fichier [Patrimoine_Arboré_data_clean.csv](Patrimoine_Arboré_data_clean.csv)
2. Appliquer ce script à chaque ligne pour obtenir le cluster de chaque arbre
3. Utiliser les colonnes Latitude/Longitude du CSV pour positionner les points sur la carte
4. Colorer les marqueurs selon le cluster/catégorie prédite

Un exemple Jupyter est disponible dans [Clusters.ipynb](Clusters.ipynb).

## Remarques

- Les valeurs de hauteur et diamètre doivent être strictement positives.
- Le paramètre `k` doit être exactement **2** ou **3**.
- Les arbres avec des dimensions manquantes ou invalides doivent être pré-filtrés avant utilisation du script.
- Les clusters n'ont pas de signification absolue (p.ex. « Petit », « Moyen », « Grand »), mais représentent des groupements basés sur les données d'entraînement.
