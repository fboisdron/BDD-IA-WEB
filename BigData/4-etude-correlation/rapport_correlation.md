# Fonctionnalité 4 – Étude des corrélations

**Jeu de données :** `Patrimoine_Arboré_data_clean.csv` — 11 418 arbres, 23 variables  
**Objectif :** identifier les liens entre variables et déterminer lesquelles influencent le plus l'âge estimé d'un arbre.

---

## 1. Matrice de corrélation – variables numériques

![Matrice de corrélation](figures/1_correlation_matrix.png)

| Variable A | Variable B | r de Pearson |
|---|---|---|
| tronc_diam | age_estim | **0.769** |
| haut_tot | age_estim | 0.601 |
| haut_tronc | age_estim | 0.490 |
| clc_nbr_diag | age_estim | 0.363 |
| haut_tot | tronc_diam | 0.685 |

**Interprétation :** Le diamètre du tronc (`tronc_diam`) est la variable numérique la plus fortement corrélée à l'âge (r = 0.769), ce qui est cohérent biologiquement — un arbre grossit en épaisseur tout au long de sa vie. La hauteur totale et la hauteur du tronc montrent également une corrélation positive modérée à forte. Le nombre de diagnostics posés (`clc_nbr_diag`) est lui aussi lié à l'âge : les arbres plus vieux font l'objet d'un suivi sanitaire plus fréquent.

---

## 2. Analyses bivariées – scatter plots vs age_estim

![Scatter plots](figures/2_scatter_vs_age_estim.png)

Chaque nuage de points est accompagné d'une droite de régression linéaire simple.

- **tronc_diam vs age_estim (r = 0.769, p < 2.2e-308)** : relation quasi-linéaire, la variable la plus prédictive.
- **haut_tot vs age_estim (r = 0.601)** : forte relation, mais avec davantage de dispersion (la hauteur est aussi contrainte par la taille de l'espace disponible).
- **haut_tronc vs age_estim (r = 0.490)** : corrélation modérée.
- **clc_nbr_diag vs age_estim (r = 0.363)** : corrélation plus faible mais statistiquement significative.

Toutes les p-values sont inférieures au seuil 0.05 (p ≈ 0), ce qui confirme la significativité de chaque corrélation.

---

## 3. Distribution de age_estim selon les catégories

![Box plots](figures/3_boxplot_age_by_category.png)

Un test ANOVA a été conduit pour chaque variable qualitative :

| Variable | F | p-value |
|---|---|---|
| fk_stadedev | très élevé | < 1e-300 |
| fk_arb_etat | élevé | < 0.05 |
| fk_situation | significatif | < 0.05 |
| feuillage | significatif | < 0.05 |
| remarquable | très élevé | < 1e-100 |

Le **stade de développement** (`fk_stadedev`) est de loin la variable qualitative la plus discriminante pour l'âge, avec une progression logique : jeune → adulte → vieux → sénescent. Les arbres **remarquables** sont significativement plus âgés que les arbres ordinaires.

---

## 4. Tests d'indépendance du chi² entre variables qualitatives

| Variable A | Variable B | chi² | p-value | V de Cramér |
|---|---|---|---|---|
| fk_stadedev | remarquable | 2769.34 | ≈ 0 | **0.497** |
| fk_stadedev | fk_arb_etat | 815.34 | 2.0e-176 | **0.270** |
| fk_stadedev | fk_situation | 295.74 | 6.7e-61 | 0.115 |
| fk_stadedev | feuillage | 173.78 | 1.9e-37 | 0.125 |
| fk_arb_etat | fk_situation | 9.24 | 9.9e-3 | 0.028 |
| feuillage | remarquable | 0.31 | 0.58 | 0.005 |

**Interprétation :**
- `fk_stadedev × remarquable` : V = 0.497 — association très forte. Les arbres remarquables sont massivement dans les stades vieux/sénescent.
- `fk_stadedev × fk_arb_etat` : V = 0.270 — les arbres abattus sont surreprésentés dans les stades jeune et sénescent (fragilité).
- `feuillage × remarquable` : p = 0.58 — **pas de lien** entre le type de feuillage (feuillu/conifère) et le caractère remarquable.

---

## 5. Mosaic plots – paires les plus significatives

![Mosaic plots](figures/5_mosaic_plots.png)

Les quatre paires de variables ayant les p-values les plus faibles au test du chi² sont représentées. La surface de chaque cellule est proportionnelle à l'effectif observé, ce qui permet de visualiser immédiatement les sur- et sous-représentations.

---

## 6. Tableaux croisés normalisés (% par ligne)

![Heatmaps tableaux croisés](figures/6_crosstab_heatmaps.png)

Ces heatmaps affichent la distribution conditionnelle (en pourcentage de ligne) pour les quatre paires les plus significatives. On observe notamment que :
- Parmi les arbres **sénescents**, la proportion d'arbres remarquables est nettement supérieure à celle des jeunes arbres.
- Les arbres **adultes** et **vieux** ont les taux d'abattage les plus faibles.

---

## 7. Rapport de corrélation eta² – toutes variables vs age_estim

![eta²](figures/7_eta_squared.png)

| Variable | eta² |
|---|---|
| fk_stadedev | **0.545** |
| fk_port | 0.171 |
| clc_quartier | 0.124 |
| remarquable | 0.116 |
| fk_situation | 0.009 |
| fk_arb_etat | 0.009 |
| feuillage | 0.000 |

L'eta² mesure la part de variance de `age_estim` expliquée par chaque variable qualitative. Le **stade de développement** explique à lui seul **54 %** de la variance de l'âge — ce qui en fait le prédicteur le plus puissant, loin devant le port (17 %) et le quartier (12 %).

---

## Synthèse – variables les plus importantes pour prédire age_estim

| Rang | Variable | Type | Mesure | Valeur |
|---|---|---|---|---|
| 1 | tronc_diam | Numérique | r de Pearson | 0.769 |
| 2 | fk_stadedev | Qualitative | eta² | 0.545 |
| 3 | haut_tot | Numérique | r de Pearson | 0.601 |
| 4 | fk_port | Qualitative | eta² | 0.171 |
| 5 | remarquable | Qualitative | eta² | 0.116 |
| 6 | haut_tronc | Numérique | r de Pearson | 0.490 |
| 7 | clc_quartier | Qualitative | eta² | 0.124 |

**Conclusion :** pour estimer l'âge d'un arbre, les variables les plus informatives sont le **diamètre du tronc** (r ≈ 0.77) et le **stade de développement** (eta² ≈ 0.54). Ces deux variables seront les prédicteurs prioritaires dans la modélisation de la Fonctionnalité 5.
