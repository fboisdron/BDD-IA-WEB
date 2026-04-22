import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
import os

INPUT_FILE = '../data/Patrimoine_Arboré_data_clean.csv'
OUTPUT_DIR = '../data/figures'

os.makedirs(OUTPUT_DIR, exist_ok=True)

df = pd.read_csv(INPUT_FILE, low_memory=False)

print("\n========== FONCTIONNALITÉ 2 : VISUALISATION GRAPHIQUE ==========")

# --- 2.1 Camembert : stade de développement ---
stade_counts = df["fk_stadedev"].value_counts()
palette = sns.color_palette("Set2", len(stade_counts))
fig, ax = plt.subplots(figsize=(8, 6))
wedges, texts = ax.pie(
    stade_counts,
    startangle=90,
    colors=palette,
)
total = stade_counts.sum()
legend_labels = [
    f"{label} — {val} ({val / total * 100:.1f}%)"
    for label, val in zip(stade_counts.index, stade_counts.values)
]
ax.legend(wedges, legend_labels,
          loc="lower center", bbox_to_anchor=(0.5, -0.2), ncol=2, fontsize=9)
ax.set_title("Répartition des arbres par stade de développement", fontweight="bold")
plt.tight_layout()
plt.savefig(f"{OUTPUT_DIR}/fig1_stade_developpement.png", dpi=150)
plt.close()
print("Figure 1 sauvegardée : stade de développement")

# --- 2.2 Histogramme : arbres par quartier ---
quartier_counts = df["clc_quartier"].value_counts().sort_values()
fig, ax = plt.subplots(figsize=(10, 7))
colors = plt.cm.YlGn(np.linspace(0.3, 0.9, len(quartier_counts)))
ax.barh(quartier_counts.index, quartier_counts.values, color=colors)
ax.set_xlabel("Nombre d'arbres")
ax.set_ylabel("Quartier")
ax.set_title("Nombre d'arbres par quartier", fontweight="bold")
plt.tight_layout()
plt.savefig(f"{OUTPUT_DIR}/fig2_arbres_par_quartier.png", dpi=150)
plt.close()
print("Figure 2 sauvegardée : arbres par quartier")

# --- 2.3 Barres : arbres par situation ---
sit_counts = df["fk_situation"].value_counts().sort_values()
fig, ax = plt.subplots(figsize=(8, 5))
bars = ax.barh(sit_counts.index, sit_counts.values,
               color=sns.color_palette("Set1", len(sit_counts)))
for bar, val in zip(bars, sit_counts.values):
    ax.text(val + 30, bar.get_y() + bar.get_height() / 2,
            str(val), va="center")
ax.set_xlabel("Nombre d'arbres")
ax.set_title("Répartition des arbres par situation", fontweight="bold")
plt.tight_layout()
plt.savefig(f"{OUTPUT_DIR}/fig3_arbres_par_situation.png", dpi=150)
plt.close()
print("Figure 3 sauvegardée : arbres par situation")

# --- 2.4 Histogramme : distribution de l'âge estimé ---
fig, ax = plt.subplots(figsize=(8, 5))
ax.hist(df["age_estim"].dropna(), bins=40, color="#52b788", edgecolor="white")
ax.set_xlabel("Âge estimé (années)")
ax.set_ylabel("Effectif")
ax.set_title("Distribution de l'âge estimé des arbres", fontweight="bold")
plt.tight_layout()
plt.savefig(f"{OUTPUT_DIR}/fig4_distribution_age.png", dpi=150)
plt.close()
print("Figure 4 sauvegardée : distribution de l'âge estimé")

# --- 2.5 Boxplot : hauteur totale par stade ---
df_box = df[df["haut_tot"] > 0]
fig, ax = plt.subplots(figsize=(8, 5))
stades = df_box["fk_stadedev"].unique()
data_box = [df_box[df_box["fk_stadedev"] == s]["haut_tot"].dropna() for s in stades]
ax.boxplot(data_box, tick_labels=stades, patch_artist=True,
           boxprops=dict(facecolor="#74c476"))
ax.set_xlabel("Stade de développement")
ax.set_ylabel("Hauteur totale (m)")
ax.set_title("Hauteur totale par stade de développement", fontweight="bold")
plt.tight_layout()
plt.savefig(f"{OUTPUT_DIR}/fig5_hauteur_par_stade.png", dpi=150)
plt.close()
print("Figure 5 sauvegardée : hauteur par stade")

# --- 2.6a Barres : top 20 secteurs ---
secteur_counts = df["clc_secteur"].value_counts().sort_values()

top20 = secteur_counts.tail(20)
fig, ax = plt.subplots(figsize=(10, 7))
colors = plt.cm.Blues(np.linspace(0.4, 0.9, len(top20)))
bars = ax.barh(top20.index, top20.values, color=colors)
for bar, val in zip(bars, top20.values):
    ax.text(val + 10, bar.get_y() + bar.get_height() / 2,
            str(val), va="center", fontsize=8)
ax.set_xlabel("Nombre d'arbres")
ax.set_ylabel("Secteur")
ax.set_title("Top 20 secteurs par nombre d'arbres", fontweight="bold")
plt.subplots_adjust(left=0.45)
plt.savefig(f"{OUTPUT_DIR}/fig6_secteurs_top20.png", dpi=150)
plt.close()
print("Figure 6 sauvegardée : top 20 secteurs")


# --- 2.7 Boxplot : largeur du tronc par type d'arbre (top 20) ---
df_tronc = df[df["tronc_diam"].notna() & (df["tronc_diam"] > 0)].copy()
top20_types = df_tronc["fk_nomtech"].value_counts().head(20).index
df_tronc = df_tronc[df_tronc["fk_nomtech"].isin(top20_types)]
ordre = df_tronc.groupby("fk_nomtech")["tronc_diam"].median().sort_values().index

fig, ax = plt.subplots(figsize=(10, 8))
data_box = [df_tronc[df_tronc["fk_nomtech"] == t]["tronc_diam"].values for t in ordre]
ax.boxplot(data_box, tick_labels=ordre, patch_artist=True, vert=False,
           boxprops=dict(facecolor="#74b9d4"), medianprops=dict(color="red", linewidth=1.5))
ax.set_xlabel("Diamètre du tronc (m)")
ax.set_title("Diamètre du tronc par type d'arbre (top 20)", fontweight="bold")
plt.subplots_adjust(left=0.35)
plt.savefig(f"{OUTPUT_DIR}/fig7_tronc_par_type.png", dpi=150)
plt.close()
print("Figure 7 sauvegardée : largeur du tronc par type d'arbre")

# --- 2.8 Arbres remarquables par quartier ---
remarq = df[df["remarquable"] == "Oui"]["clc_quartier"].value_counts().sort_values()
fig, ax = plt.subplots(figsize=(9, 5))
ax.barh(remarq.index, remarq.values, color="#d4a017")
ax.set_xlabel("Nombre")
ax.set_title("Arbres remarquables par quartier", fontweight="bold")
plt.tight_layout()
plt.savefig(f"{OUTPUT_DIR}/fig8_remarquables_quartier.png", dpi=150)
plt.close()
print("Figure 8 sauvegardée : arbres remarquables par quartier")
