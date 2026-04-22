import pandas as pd
import geopandas as gpd
import matplotlib.pyplot as plt
from shapely.geometry import Point
import contextily as ctx

# === 1. Charger le fichier ===
file_path = "../data/Patrimoine_Arboré_data_clean.csv"
df = pd.read_csv(file_path)

# === 2. Colonnes (index Excel → Python) ===
# U = index 20, V = 21, W = 22
remarkable_col = "remarquable"
lat_col = "Latitude"
lon_col = "Longitude"

# === 3. Nettoyage (au cas où) ===
df = df.dropna(subset=[lon_col, lat_col])

# === 4. Géométrie ===
geometry = [Point(xy) for xy in zip(df[lon_col], df[lat_col])]
gdf = gpd.GeoDataFrame(df, geometry=geometry, crs="EPSG:4326")

# Projection Web Mercator pour fond OSM
gdf = gdf.to_crs(epsg=3857)

# === 5. Séparer les arbres ===
gdf_remarkable = gdf[gdf[remarkable_col] == "Oui"]
gdf_normal = gdf[gdf[remarkable_col] != "Oui"]

# === 6. Plot ===
fig, ax = plt.subplots(figsize=(10, 10))

# Arbres normaux (bleu)
gdf_normal.plot(
    ax=ax,
    color="blue",
    markersize=10,
    label="Arbres"
)

# Arbres remarquables (rouge)
gdf_remarkable.plot(
    ax=ax,
    color="red",
    markersize=25,
    label="Arbres remarquables"
)

# Fond OpenStreetMap
ctx.add_basemap(ax, source=ctx.providers.OpenStreetMap.Mapnik)

# === 7. Mise en forme ===
ax.set_title("Carte des arbres à Saint-Quentin", fontsize=14)
ax.legend()
ax.axis("off")

# === 8. Export PNG ===
output_file = "carte_arbres_saint_quentin.png"
plt.savefig(output_file, dpi=300, bbox_inches="tight")

print(f"Carte enregistrée : {output_file}")