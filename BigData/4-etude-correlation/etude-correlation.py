import os
import warnings

import matplotlib.pyplot as plt
import numpy as np
import pandas as pd
import scipy.stats as stats
import seaborn as sns
import statsmodels.api as sm
from statsmodels.graphics.mosaicplot import mosaic

warnings.filterwarnings("ignore")

INPUT_FILE = "../data/Patrimoine_Arboré_data_clean.csv"
FIGURES_DIR = "figures"
os.makedirs(FIGURES_DIR, exist_ok=True)

NUMERIC_COLS = ["haut_tot", "haut_tronc", "tronc_diam", "age_estim", "clc_nbr_diag"]
QUAL_COLS = [
    "fk_stadedev",
    "fk_arb_etat",
    "fk_situation",
    "feuillage",
    "remarquable",
    "fk_port",
    "fk_pied",
    "clc_quartier",
]
TARGET = "age_estim"


# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

def save(fig, name):
    path = os.path.join(FIGURES_DIR, name)
    fig.savefig(path, bbox_inches="tight", dpi=150)
    plt.close(fig)
    print(f"  -> {path}")


def filter_na(df, cols):
    """Garde uniquement les lignes sans 'N/A' (catégorie sentinelle) sur les colonnes cols."""
    mask = pd.Series(True, index=df.index)
    for c in cols:
        if c in df.columns:
            mask &= df[c] != "N/A"
    return df[mask]


# ---------------------------------------------------------------------------
# 1. Matrice de corrélation des variables numériques
# ---------------------------------------------------------------------------

def plot_correlation_matrix(df):
    print("\n[1] Matrice de corrélation des variables numériques")
    num_df = df[NUMERIC_COLS].dropna()

    corr = num_df.corr()
    print(corr.round(3).to_string())

    fig, ax = plt.subplots(figsize=(8, 6))
    sns.heatmap(
        corr,
        annot=True,
        fmt=".2f",
        cmap="coolwarm",
        center=0,
        linewidths=0.5,
        ax=ax,
    )
    ax.set_title("Matrice de corrélation – variables numériques")
    save(fig, "1_correlation_matrix.png")
    return corr


# ---------------------------------------------------------------------------
# 2. Scatter plots : variables numériques vs age_estim
# ---------------------------------------------------------------------------

def plot_scatter_vs_target(df):
    print(f"\n[2] Scatter plots des variables numériques vs {TARGET}")
    others = [c for c in NUMERIC_COLS if c != TARGET]

    fig, axes = plt.subplots(2, 2, figsize=(12, 10))
    axes = axes.flatten()

    for i, col in enumerate(others):
        sub = df[[col, TARGET]].dropna()
        r, p = stats.pearsonr(sub[col], sub[TARGET])
        axes[i].scatter(sub[col], sub[TARGET], alpha=0.3, s=10, color="steelblue")
        # droite de régression
        m, b = np.polyfit(sub[col], sub[TARGET], 1)
        x_line = np.linspace(sub[col].min(), sub[col].max(), 200)
        axes[i].plot(x_line, m * x_line + b, color="crimson", linewidth=1.5)
        axes[i].set_xlabel(col)
        axes[i].set_ylabel(TARGET)
        axes[i].set_title(f"{col} vs {TARGET}\nr={r:.3f}  p={p:.2e}")
        print(f"  {col:20s}  r={r:.3f}  p={p:.2e}")

    fig.suptitle(f"Corrélations bivariées avec {TARGET}", fontsize=13, y=1.01)
    fig.tight_layout()
    save(fig, "2_scatter_vs_age_estim.png")


# ---------------------------------------------------------------------------
# 3. Box plots : age_estim par variable qualitative
# ---------------------------------------------------------------------------

def plot_boxplots_qual_vs_target(df):
    print(f"\n[3] Box plots – {TARGET} par variable qualitative")
    small_cats = ["fk_stadedev", "fk_arb_etat", "fk_situation", "feuillage", "remarquable"]

    fig, axes = plt.subplots(2, 3, figsize=(16, 10))
    axes = axes.flatten()

    for i, col in enumerate(small_cats):
        sub = filter_na(df, [col])[[col, TARGET]].dropna()
        order = sub.groupby(col)[TARGET].median().sort_values().index.tolist()
        sns.boxplot(data=sub, x=col, y=TARGET, order=order, ax=axes[i], palette="Set2")
        axes[i].set_title(f"{TARGET} par {col}")
        axes[i].set_xlabel(col)
        axes[i].set_ylabel(TARGET)
        axes[i].tick_params(axis="x", rotation=20)

    # ANOVA résumé dans le dernier axe
    ax = axes[5]
    ax.axis("off")
    lines = [f"{'Variable':<22} {'F':>8} {'p-value':>12}"]
    lines.append("-" * 44)
    for col in small_cats:
        sub = filter_na(df, [col])[[col, TARGET]].dropna()
        groups = [g[TARGET].values for _, g in sub.groupby(col)]
        if len(groups) >= 2:
            F, p = stats.f_oneway(*groups)
            lines.append(f"{col:<22} {F:8.2f} {p:12.2e}")
    ax.text(0.05, 0.95, "\n".join(lines), transform=ax.transAxes,
            va="top", family="monospace", fontsize=9)
    ax.set_title("ANOVA – test de différence de moyennes")

    fig.suptitle(f"Distribution de {TARGET} selon les catégories", fontsize=13, y=1.01)
    fig.tight_layout()
    save(fig, "3_boxplot_age_by_category.png")


# ---------------------------------------------------------------------------
# 4. Tableaux croisés + tests du chi² entre variables qualitatives
# ---------------------------------------------------------------------------

QUAL_CHI2_PAIRS = [
    ("fk_stadedev", "fk_arb_etat"),
    ("fk_arb_etat", "fk_situation"),
    ("fk_stadedev", "remarquable"),
]


def chi2_tests(df):
    print("\n[4] Tests du chi² entre variables qualitatives")
    results = []

    for col_a, col_b in QUAL_CHI2_PAIRS:
        sub = filter_na(df, [col_a, col_b])[[col_a, col_b]].dropna()
        ct = pd.crosstab(sub[col_a], sub[col_b])
        chi2, p, dof, expected = stats.chi2_contingency(ct)
        cramer_v = np.sqrt(chi2 / (sub.shape[0] * (min(ct.shape) - 1)))
        results.append(
            {"var_A": col_a, "var_B": col_b, "chi2": chi2, "p": p, "dof": dof, "V_Cramer": cramer_v}
        )
        print(f"  {col_a} x {col_b}: chi2={chi2:.2f}  p={p:.2e}  V={cramer_v:.3f}")

    results_df = pd.DataFrame(results).sort_values("p")
    return results_df


# ---------------------------------------------------------------------------
# 5. Mosaic plots des paires les plus significatives
# ---------------------------------------------------------------------------

def plot_mosaics(df, chi2_results):
    print("\n[5] Mosaic plots des paires qualitatives sélectionnées")
    selected_pairs = chi2_results[["var_A", "var_B"]].values.tolist()
    n_pairs = len(selected_pairs)
    fig, axes = plt.subplots(1, n_pairs, figsize=(6 * n_pairs, 5))
    if n_pairs == 1:
        axes = [axes]

    for i, (col_a, col_b) in enumerate(selected_pairs):
        sub = filter_na(df, [col_a, col_b])[[col_a, col_b]].dropna()
        ct = pd.crosstab(sub[col_a], sub[col_b])
        chi2, p, _, _ = stats.chi2_contingency(ct)

        # mosaic() attend un dict de fréquences
        freq = sub.groupby([col_a, col_b]).size()
        mosaic(freq, ax=axes[i], title=f"{col_a} × {col_b}\nchi²={chi2:.1f}  p={p:.2e}", gap=0.02)
        axes[i].set_xlabel(col_b)
        axes[i].set_ylabel(col_a)

    fig.suptitle("Mosaic plots – paires qualitatives sélectionnées", fontsize=13, y=1.01)
    fig.tight_layout()
    save(fig, "5_mosaic_plots.png")


# ---------------------------------------------------------------------------
# 6. Heatmap des tableaux croisés normalisés (% ligne)
# ---------------------------------------------------------------------------

def plot_crosstab_heatmaps(df):
    print("\n[6] Heatmaps des tableaux croisés")
    pairs = QUAL_CHI2_PAIRS
    n_pairs = len(pairs)
    fig, axes = plt.subplots(1, n_pairs, figsize=(6 * n_pairs, 5))
    if n_pairs == 1:
        axes = [axes]

    for i, (col_a, col_b) in enumerate(pairs):
        sub = filter_na(df, [col_a, col_b])[[col_a, col_b]].dropna()
        ct = pd.crosstab(sub[col_a], sub[col_b], normalize="index") * 100
        sns.heatmap(ct, annot=True, fmt=".1f", cmap="YlOrRd", ax=axes[i], linewidths=0.5)
        axes[i].set_title(f"{col_a} × {col_b} (% ligne)")
        axes[i].set_xlabel(col_b)
        axes[i].set_ylabel(col_a)

    fig.suptitle("Tableaux croisés normalisés (% par ligne)", fontsize=13, y=1.01)
    fig.tight_layout()
    save(fig, "6_crosstab_heatmaps.png")


# ---------------------------------------------------------------------------
# 7. Corrélation numérique x catégorie : eta² (rapport de corrélation)
# ---------------------------------------------------------------------------

def eta_squared(groups_series, values_series):
    """Calcule l'eta² (rapport de corrélation) entre une variable qualitative et quantitative."""
    grand_mean = values_series.mean()
    ss_total = ((values_series - grand_mean) ** 2).sum()
    ss_between = sum(
        len(g) * (g.mean() - grand_mean) ** 2
        for g in [values_series[groups_series == cat] for cat in groups_series.unique()]
    )
    return ss_between / ss_total if ss_total > 0 else 0.0


def plot_eta_squared(df):
    print(f"\n[7] Rapport de corrélation eta² – variables qualitatives vs {TARGET}")
    qual_for_eta = ["fk_stadedev", "fk_arb_etat", "fk_situation", "feuillage", "remarquable", "fk_port", "clc_quartier"]
    etas = {}

    for col in qual_for_eta:
        sub = filter_na(df, [col])[[col, TARGET]].dropna()
        e2 = eta_squared(sub[col], sub[TARGET])
        etas[col] = e2
        print(f"  eta²({col}) = {e2:.4f}")

    fig, ax = plt.subplots(figsize=(8, 5))
    sorted_etas = dict(sorted(etas.items(), key=lambda x: x[1], reverse=True))
    bars = ax.barh(list(sorted_etas.keys()), list(sorted_etas.values()), color="steelblue")
    ax.bar_label(bars, fmt="%.3f", padding=3)
    ax.set_xlabel("eta²")
    ax.set_title(f"Rapport de corrélation eta² – variables vs {TARGET}")
    ax.set_xlim(0, max(etas.values()) * 1.2)
    fig.tight_layout()
    save(fig, "7_eta_squared.png")


# ---------------------------------------------------------------------------
# 8. Régression linéaire multiple : tronc_diam + haut_tronc → age_estim
# ---------------------------------------------------------------------------

def regression_morphologique(df):
    print("\n[8] Régression linéaire multiple : tronc_diam + haut_tronc → age_estim")
    sub = df[["tronc_diam", "haut_tronc", TARGET]].dropna()

    X = sm.add_constant(sub[["tronc_diam", "haut_tronc"]])
    model = sm.OLS(sub[TARGET], X).fit()
    print(model.summary())

    r2    = model.rsquared
    rmse  = np.sqrt(model.mse_resid)
    coef  = model.params
    pvals = model.pvalues
    print(f"\nR²   = {r2:.4f}")
    print(f"RMSE = {rmse:.2f} ans")
    print(f"Coef tronc_diam : {coef['tronc_diam']:+.3f}  (p={pvals['tronc_diam']:.2e})")
    print(f"Coef haut_tronc : {coef['haut_tronc']:+.3f}  (p={pvals['haut_tronc']:.2e})")

    pred = model.fittedvalues

    fig, axes = plt.subplots(1, 3, figsize=(16, 5))

    # --- Panneau 1 : tronc_diam vs age_estim avec droite de régression ---
    for ax, var, label, color in [
        (axes[0], "tronc_diam", "Diamètre du tronc (m)", "#2196F3"),
        (axes[1], "haut_tronc", "Hauteur du tronc (m)",  "#4CAF50"),
    ]:
        ax.scatter(sub[var], sub[TARGET], alpha=0.15, s=6, color=color)
        x_line = np.linspace(sub[var].min(), sub[var].max(), 200)
        # droite marginale (régression simple pour la lisibilité du graphe)
        r_simple, _ = stats.pearsonr(sub[var], sub[TARGET])
        m, b = np.polyfit(sub[var], sub[TARGET], 1)
        ax.plot(x_line, m * x_line + b, color="crimson", linewidth=2)
        ax.set_xlabel(label, fontsize=11)
        ax.set_ylabel("Âge estimé (ans)", fontsize=11)
        ax.set_title(f"r = {r_simple:+.3f}", fontsize=12, fontweight="bold")
        ax.annotate(f"y = {m:.1f}x + {b:.1f}", xy=(0.05, 0.92),
                    xycoords="axes fraction", fontsize=9, color="crimson")

    # --- Panneau 3 : observé vs prédit (modèle multiple) ---
    axes[2].scatter(sub[TARGET], pred, alpha=0.2, s=6, color="#9C27B0")
    lim = [sub[TARGET].min(), sub[TARGET].max()]
    axes[2].plot(lim, lim, color="crimson", linestyle="--", linewidth=1.8, label="Prédiction parfaite")
    axes[2].set_xlabel("Âge observé (ans)", fontsize=11)
    axes[2].set_ylabel("Âge prédit (ans)", fontsize=11)
    axes[2].set_title(f"Modèle multiple\nR² = {r2:.3f}  —  RMSE = {rmse:.1f} ans",
                      fontsize=12, fontweight="bold")
    axes[2].legend(fontsize=9)

    fig.suptitle("Régression linéaire multiple : tronc_diam + haut_tronc → age_estim",
                 fontsize=13, fontweight="bold", y=1.02)
    fig.tight_layout()
    save(fig, "8_regression_morphologique.png")

    return r2, rmse


# ---------------------------------------------------------------------------
# Main
# ---------------------------------------------------------------------------

def main():
    df = pd.read_csv(INPUT_FILE)
    print(f"Données chargées : {df.shape[0]} lignes, {df.shape[1]} colonnes")

    corr = plot_correlation_matrix(df)
    plot_scatter_vs_target(df)
    plot_boxplots_qual_vs_target(df)
    chi2_results = chi2_tests(df)
    plot_mosaics(df, chi2_results)
    plot_crosstab_heatmaps(df)
    plot_eta_squared(df)
    r2_morph, rmse_morph = regression_morphologique(df)

    print("\n=== Résumé des corrélations avec age_estim ===")
    print("Variables numériques (Pearson r) :")
    for col in [c for c in NUMERIC_COLS if c != TARGET]:
        sub = df[[col, TARGET]].dropna()
        r, p = stats.pearsonr(sub[col], sub[TARGET])
        print(f"  {col:<20s} r={r:+.3f}  p={p:.2e}")

    print("\nVariables qualitatives (eta²) :")
    for col in ["fk_stadedev", "fk_arb_etat", "fk_situation", "feuillage", "remarquable"]:
        sub = filter_na(df, [col])[[col, TARGET]].dropna()
        print(f"  {col:<20s} eta²={eta_squared(sub[col], sub[TARGET]):.4f}")

    print("\nTerminé. Figures enregistrées dans :", FIGURES_DIR)


if __name__ == "__main__":
    main()
