# BDD-IA-WEB

Le dépôt contient désormais deux couches complémentaires :

- la chaîne BigData/IA qui produit et exploite le CSV clean,
- une base d’application web dans `webapp/` pour Saint-Quentin.

## Application web

L’application PHP/AJAX se trouve dans [webapp/](webapp).

Elle fournit :

- un header et un footer communs,
- une page d’accueil,
- une page d’ajout d’arbres,
- une page de cartes,
- une page de formulaires pour les besoins clients,
- un schéma PostgreSQL et un script d’import du CSV clean.

## Préparation BigData

Pour le nettoyage des données, le script principal est [BigData/1-nettoyage/nettoyage.py](BigData/1-nettoyage/nettoyage.py).

### Prérequis

- Python 3 installé (`python3 --version`)
- `pip` disponible

### 1) Créer et activer un environnement virtuel (venv)

Depuis la racine du projet :

```bash
cd BigData
python3 -m venv .venv
source .venv/bin/activate
```

### 2) Installer les dépendances

```bash
pip install --upgrade pip
pip install -r nettoyage/requirements.txt
```

### 3) Exécuter le script

Le script doit être lancé depuis `BigData/data` pour que les chemins relatifs fonctionnent correctement.

```bash
cd nettoyage
python nettoyage.py
```

### 4) Fichiers générés

Après exécution, les fichiers suivants sont générés dans `BigData/data` :

- `Patrimoine_Arboré_data_clean.csv`
- `Patrimoine_Arboré_data_clean.xlsx`
- `rapport_nettoyage.txt`

### 5) Désactiver le venv

```bash
deactivate
```

