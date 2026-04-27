# Utiliser le script de prédiction

Ce dossier contient un script Python qui permet de prédire si un arbre est susceptible d'être déraciné pendant une tempête.

Le script à exécuter est [predire_alerte.py](predire_alerte.py). Par défaut, il charge le modèle [random_forest_alerte.pkl](random_forest_alerte.pkl), présent dans le même dossier.

## Prérequis

Installez les dépendances du projet avec :

```bash
pip install -r requirements.txt
```

Le script attend Python 3 et les bibliothèques `pandas`, `joblib` et `scikit-learn`.

## Lancer une prédiction

Placez-vous dans le dossier `IA/3-Systeme-alerte-tempête`, puis exécutez le script avec toutes les variables d'entrée obligatoires :

```bash
python predire_alerte.py \
	--haut_tot 12.5 \
	--haut_tronc 3.2 \
	--tronc_diam 0.45 \
	--age_estim 30 \
	--fk_stadedev adulte \
	--fk_port rideau \
	--fk_pied gazon \
	--fk_situation alignement \
	--fk_revetement non \
	--feuillage feuillu
```

Le script affiche :

- les valeurs envoyées au modèle,
- le verdict `ALERTE` ou `PAS D'ALERTE`,
- la probabilité estimée de risque.

## Paramètre du modèle

Par défaut, le script charge `random_forest_alerte.pkl` dans le dossier courant. Vous pouvez préciser un autre modèle avec :

```bash
python predire_alerte.py --model chemin/vers/autre_modele.pkl ...
```

## Paramètres obligatoires

Tous les paramètres suivants doivent être fournis :

- `--haut_tot` : hauteur totale de l'arbre,
- `--haut_tronc` : hauteur du tronc,
- `--tronc_diam` : diamètre du tronc,
- `--age_estim` : âge estimé de l'arbre,
- `--fk_stadedev` : stade de développement,
- `--fk_port` : port de l'arbre,
- `--fk_pied` : type de pied,
- `--fk_situation` : situation de l'arbre,
- `--fk_revetement` : revêtement,
- `--feuillage` : type de feuillage.

Les valeurs textuelles sont automatiquement nettoyées par le script avec suppression des espaces autour et conversion en minuscules.

## Remarque

Les valeurs catégorielles doivent correspondre à celles attendues par le modèle entraîné. Si vous n'êtes pas sûr des modalités exactes, reprenez les valeurs utilisées lors de la préparation des données.
