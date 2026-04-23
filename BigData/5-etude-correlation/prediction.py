import pandas as pd
from sklearn.linear_model import LogisticRegression

INPUT_FILE = "../data/Patrimoine_Arboré_data_clean.csv"
OUTPUT_FILE = "../data/resultat_regression_logistique.csv"

# 1) Charger les données
df = pd.read_csv(INPUT_FILE)

# 2) Variable cible déjà binaire
y = df["fk_arb_etat"]

# Supprimer les lignes sans cible
mask = y.notna()
df = df[mask].copy()
y = df["fk_arb_etat"]

# 3) Variables explicatives
X = df.drop(columns=["fk_arb_etat"], errors="ignore")

# 4) Transformer les colonnes texte en variables numériques
X = pd.get_dummies(X, drop_first=True)

# 5) Remplacer les valeurs manquantes par la moyenne
X = X.fillna(X.mean())

# 6) Entraîner la régression logistique
model = LogisticRegression(max_iter=5000)
model.fit(X, y)

# 7) Probabilité d'abattage
df["proba_abattage"] = model.predict_proba(X)[:, 1]

# 8) Prédiction finale
df["prediction_abattage"] = model.predict(X)

# 9) Export
df.to_csv(OUTPUT_FILE, index=False, encoding="utf-8-sig")

print("Régression logistique terminée.")
print(f"Résultat exporté : {OUTPUT_FILE}")

print("\nTop 10 arbres avec la plus forte probabilité d'abattage :")
print(df[["proba_abattage", "prediction_abattage"]].sort_values("proba_abattage", ascending=False).head(10))