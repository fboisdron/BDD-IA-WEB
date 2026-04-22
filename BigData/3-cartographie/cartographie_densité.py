import pandas as pd
import geopandas as gpd
import matplotlib.pyplot as plt
from shapely.geometry import Point, box
import contextily as ctx
import numpy as np

# === 1. Charger ===
file_path = "../data/Patrimoine_Arboré_data_clean.csv"
df = pd.read_csv(file_path)

# === 2. Colonnes ===
lat_col = "Latitude"
lon_col = "Longitude"

# === 3. Nettoyage ===
df = df.dropna(subset=[lon_col, lat_col])

# === 4. GeoDataFrame ===
geometry = [Point(xy) for xy in zip(df[lon_col], df[lat_col])]
gdf = gpd.GeoDataFrame(df, geometry=geometry, crs="EPSG:4326")

# Projection métrique
gdf = gdf.to_crs(epsg=3857)

# =========================================================
# 5. Création grille (carroyage)
# =========================================================

# Taille des cellules
cell_size = 200 

xmin, ymin, xmax, ymax = gdf.total_bounds

grid_cells = []
for x in np.arange(xmin, xmax, cell_size):
    for y in np.arange(ymin, ymax, cell_size):
        grid_cells.append(box(x, y, x + cell_size, y + cell_size))

grid = gpd.GeoDataFrame(geometry=grid_cells, crs=gdf.crs)

# =========================================================
# 6. Comptage des arbres par cellule
# =========================================================

joined = gpd.sjoin(gdf, grid, predicate="within")

counts = joined.groupby("index_right").size()

grid["nb_arbres"] = counts
grid["nb_arbres"] = grid["nb_arbres"].fillna(0)

# =========================================================
# 7. Densité (arbres par hectare)
# =========================================================

# surface cellule en m²
grid["surface_m2"] = grid.geometry.area

# densité arbres / hectare (1 ha = 10 000 m²)
grid["densite"] = grid["nb_arbres"] / (grid["surface_m2"] / 10000)

# =========================================================
# 8. Plot
# =========================================================

fig, ax = plt.subplots(figsize=(10, 10))

grid.plot(
    column="densite",
    cmap="YlGn",  # vert
    linewidth=0,
    alpha=0.7,
    legend=True,
    ax=ax
)

# Fond OSM
ctx.add_basemap(ax, source=ctx.providers.OpenStreetMap.Mapnik)

# =========================================================
# Finitions
# =========================================================

ax.set_title("Densité d'arbres (arbres par hectare) à Saint-Quentin", fontsize=14)
ax.axis("off")

# Export
plt.savefig("carte_densite_arbres.png", dpi=300, bbox_inches="tight")

print("Carte de densité générée")