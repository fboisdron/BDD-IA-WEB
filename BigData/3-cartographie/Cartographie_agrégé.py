import pandas as pd
import geopandas as gpd
import matplotlib.pyplot as plt
from shapely.geometry import Point
import contextily as ctx
from matplotlib.lines import Line2D
import numpy as np

# === 1. Charger ===
file_path = "../data/Patrimoine_Arboré_data_clean.csv"
df = pd.read_csv(file_path)

# === 2. Colonnes ===
lat_col = "Latitude"
lon_col = "Longitude"
quartier_col = "clc_quartier"
secteur_col = "clc_secteur"

# === 3. Nettoyage ===
df = df.dropna(subset=[lon_col, lat_col, quartier_col, secteur_col])

# =========================================================
# QUARTIERS
# =========================================================

df_q = df.groupby(quartier_col).size().reset_index(name="nb_arbres")
df_q_centroid = df.groupby(quartier_col)[[lon_col, lat_col]].mean().reset_index()
df_q = df_q.merge(df_q_centroid, on=quartier_col)

gdf_q = gpd.GeoDataFrame(
    df_q,
    geometry=[Point(xy) for xy in zip(df_q[lon_col], df_q[lat_col])],
    crs="EPSG:4326"
).to_crs(epsg=3857)

# =========================================================
# SECTEURS
# =========================================================

df_s = df.groupby(secteur_col).size().reset_index(name="nb_arbres")
df_s_centroid = df.groupby(secteur_col)[[lon_col, lat_col]].mean().reset_index()
df_s = df_s.merge(df_s_centroid, on=secteur_col)

gdf_s = gpd.GeoDataFrame(
    df_s,
    geometry=[Point(xy) for xy in zip(df_s[lon_col], df_s[lat_col])],
    crs="EPSG:4326"
).to_crs(epsg=3857)

# =========================================================
# ÉCHELLE
# =========================================================

max_global = max(gdf_q["nb_arbres"].max(), gdf_s["nb_arbres"].max())

def scale_size(val):
    return (np.sqrt(val) / np.sqrt(max_global)) * 1000

gdf_q["size"] = gdf_q["nb_arbres"].apply(scale_size)
gdf_s["size"] = gdf_s["nb_arbres"].apply(scale_size)

# =========================================================
# PLOT
# =========================================================

fig, ax = plt.subplots(figsize=(10, 10))

gdf_q.plot(
    ax=ax,
    color="blue",
    markersize=gdf_q["size"],
    alpha=0.6,
    zorder=1
)

gdf_s.plot(
    ax=ax,
    color="red",
    markersize=gdf_s["size"],
    alpha=0.5,
    edgecolor="black",
    linewidth=1,
    zorder=2
)

ctx.add_basemap(ax, source=ctx.providers.OpenStreetMap.Mapnik)

# =========================================================
# LÉGENDE
# =========================================================

# 1️⃣ Légende catégories
legend_colors = [
    Line2D([0], [0], marker='o', color='w',
           label='Quartiers',
           markerfacecolor='blue',
           markersize=10),

    Line2D([0], [0], marker='o', color='w',
           label='Secteurs',
           markerfacecolor='red',
           markeredgecolor='black',
           markersize=10)
]

# 2️⃣ Légende tailles (valeurs exemples)
valeurs = [100, 500, 1000]  # ⚠️ adapte selon ton dataset

legend_sizes = [
    plt.scatter([], [], s=scale_size(v), color='gray', alpha=0.6,
                label=f"{v} arbres")
    for v in valeurs
]

# Fusion des deux légendes
ax.legend(handles=legend_colors + legend_sizes, loc='upper right', title="Légende")

# =========================================================
# Export
# =========================================================

ax.set_title("Nombre d'arbres par quartier (bleu) et secteur (rouge)", fontsize=14)
ax.axis("off")

plt.savefig("carte_arbres_quartiers_secteurs.png", dpi=300, bbox_inches="tight")

print("Carte générée")