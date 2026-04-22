import pandas as pd
import geopandas as gpd
import matplotlib.pyplot as plt
from shapely.geometry import Point
import contextily as ctx
from matplotlib.lines import Line2D

# === 1. Charger ===
file_path = "../data/Patrimoine_Arboré_data_clean.csv"
df = pd.read_csv(file_path)

# === 2. Colonnes ===
lat_col = "Latitude"
lon_col = "Longitude"
stade_col = "fk_stadedev"

# === 3. Nettoyage ===
df = df.dropna(subset=[lon_col, lat_col, stade_col])

# === 4. Mapping des stades ===
mapping = {
    "Jeune": "Jeune",
    "Adulte": "Adulte",
    "Vieux": "Vieux",
    "Senescent": "Senescent",
}

df["stade"] = df[stade_col].map(mapping)

# Supprimer les valeurs inconnues
df = df.dropna(subset=["stade"])

# === 5. GeoDataFrame ===
geometry = [Point(xy) for xy in zip(df[lon_col], df[lat_col])]
gdf = gpd.GeoDataFrame(df, geometry=geometry, crs="EPSG:4326")
gdf = gdf.to_crs(epsg=3857)

# === 6. Couleurs ===
colors = {
    "Jeune": "green",
    "Adulte": "blue",
    "Vieux": "orange",
    "Senescent": "red"
}

# =========================================================
# PLOT
# =========================================================

fig, ax = plt.subplots(figsize=(10, 10))

for stade, color in colors.items():
    subset = gdf[gdf["stade"] == stade]
    subset.plot(
        ax=ax,
        color=color,
        markersize=8,
        label=stade,
        alpha=0.7
    )

# Fond OSM
ctx.add_basemap(ax, source=ctx.providers.OpenStreetMap.Mapnik)

# =========================================================
# LÉGENDE PROPRE
# =========================================================

legend_elements = [
    Line2D([0], [0], marker='o', color='w', label='Jeune',
           markerfacecolor='green', markersize=8),

    Line2D([0], [0], marker='o', color='w', label='Adulte',
           markerfacecolor='blue', markersize=8),

    Line2D([0], [0], marker='o', color='w', label='Vieux',
           markerfacecolor='orange', markersize=8),

    Line2D([0], [0], marker='o', color='w', label='Sénescent',
           markerfacecolor='red', markersize=8)
]

ax.legend(handles=legend_elements, title="Stade de développement")

# =========================================================
# Finitions
# =========================================================

ax.set_title("Stade de développement des arbres à Saint-Quentin", fontsize=14)
ax.axis("off")

# Export
plt.savefig("carte_stades_arbres.png", dpi=300, bbox_inches="tight")

print("Carte générée")