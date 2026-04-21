# BDD-IA-WEB

## Utiliser le script `nettoyage.py`

Le script de nettoyage se trouve dans `BigData/data/nettoyage.py`.

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

Après activation, le terminal doit afficher `(.venv)` au début de la ligne.

### 2) Installer les dépendances

```bash
pip install --upgrade pip
pip install -r nettoyage/requirements.txt
```

### 3) Exécuter le script

Le script doit être lancé depuis `BigData/data` pour que les chemins relatifs fonctionnent correctement.

```bash
cd data
python nettoyage.py
```

### 4) Fichiers générés

Après exécution, les fichiers suivants sont générés dans `BigData/data` :

- `Patrimoine_Arboré_data_clean.csv`
- `Patrimoine_Arboré_data_clean.xlsx`
- `rapport_nettoyage.txt`

### 5) Désactiver le venv

Quand vous avez terminé :

```bash
deactivate
```

