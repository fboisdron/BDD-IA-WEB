# Fonctionnalité 5 – Prédiction et analyse de régression

**Jeu de données :** `Patrimoine_Arboré_data_clean.csv` — 11 418 arbres, 23 variables  
**Outil :** R 4.4.2 — librairies `MASS`, `dplyr`, `ggplot2`, `car`, `pROC`, `corrplot`  
**Script :** `prediction.R` — Figures générées dans `figures/`

---

## Partie A – Régression linéaire : prédire l'âge estimé

### A.1 – Corrélations numériques

![Corrplot](figures/A1_corrplot.png)

Le diamètre du tronc est la variable numérique la plus corrélée à l'âge (r = 0.769), devant la hauteur totale (r = 0.601) et la hauteur de tronc (r = 0.490). Ces corrélations élevées justifient leur inclusion comme prédicteurs principaux.

---

### A.2 – Modèle complet puis sélection par AIC (stepwise)

Le modèle complet inclut les 4 variables numériques et 4 variables qualitatives. La sélection `stepAIC` (bidirectionnelle) retire `fk_situation` (non significative, p > 0.1), aboutissant au modèle final :

```
age_estim ~ haut_tot + haut_tronc + tronc_diam + clc_nbr_diag
           + fk_stadedev + feuillage + remarquable
```

Le modèle explique **74.5 % de la variance** de l'âge estimé (R² = 0.7449, R² ajusté = 0.7446) avec une erreur moyenne de **10.3 ans** (RMSE). Le F-statistic de 3622 (p < 2.2e-16) confirme la significativité globale du modèle sur 11 177 observations.

---

### A.3 – Coefficients et significativité

![Coefficients](figures/A5_coefficients.png)

Toutes les variables retenues sont significatives à p < 0.001. Les effets les plus importants sont les suivants. Un arbre classé **remarquable** est estimé 30 ans plus vieux toutes choses égales par ailleurs — c'est le coefficient le plus élevé en valeur absolue. Le stade **jeune** retranche 16 ans par rapport au stade adulte (référence), tandis que les stades **vieux** et **sénescent** en ajoutent respectivement 9 et 8. Chaque mètre supplémentaire de **diamètre de tronc** ajoute 15.5 ans, ce qui confirme son rôle de prédicteur morphologique central. Les **feuillus** sont estimés 4 ans plus vieux que les conifères, et chaque **diagnostic** supplémentaire ajoute 2.6 ans (signe d'un suivi accru des arbres âgés). La hauteur totale a un effet légèrement négatif (−0.11) dû à sa redondance avec le diamètre.

---

### A.4 – Diagnostics du modèle

![Résidus vs ajustés](figures/A4a_residus_vs_ajustes.png)
![Q-Q plot](figures/A4b_qqplot.png)

Le graphique résidus/valeurs ajustées ne montre pas de structure systématique marquée, bien qu'une légère hétéroscédasticité apparaisse pour les grands âges (peu représentés). Le Q-Q plot confirme la normalité des résidus au centre de la distribution ; les queues présentent quelques valeurs extrêmes, attendues pour des arbres très anciens.

---

### A.5 – Valeurs observées vs prédites

![Observées vs prédites](figures/A6_observed_vs_predicted.png)

Les prédictions suivent bien la diagonale idéale. La dispersion s'élargit pour les âges supérieurs à 80 ans, ce qui est cohérent avec le faible effectif dans ces classes.

---

## Partie B – Régression logistique : identifier les arbres à abattre

La variable cible est `fk_arb_etat` encodée en binaire (ABATTU = 1, EN PLACE = 0). Le jeu de données est très déséquilibré : 860 abattus (7.7 %) pour 10 317 arbres en place. Le découpage est 70 % entraînement / 30 % test, graine fixée à 42.

### B.1 – Modèle sélectionné (stepAIC)

```
abattu ~ haut_tot + haut_tronc + age_estim + clc_nbr_diag
       + fk_stadedev + fk_situation + feuillage + remarquable
```

`tronc_diam` a été retiré par le stepAIC en raison de sa colinéarité avec `age_estim`.

---

### B.2 – Performances sur le jeu de test

Sur le jeu de test, le modèle classe correctement 3 092 des 3 094 arbres en place (quasi-parfait), mais ne détecte que 25 des 260 abattus réels. L'accuracy de 92.9 % est trompeuse : elle reflète surtout la classe majoritaire. Le rappel de 9.6 % et le F1-score de 0.174 traduisent la difficulté à détecter les abattus au seuil par défaut de 0.5. L'**AUC de 0.716** reste satisfaisante et indique une discrimination correcte entre les deux classes. Pour un usage opérationnel, il faudrait abaisser le seuil de décision ou rééchantillonner les classes (ex. SMOTE).

---

### B.3 – Courbe ROC

![Courbe ROC](figures/B4_roc_curve.png)

L'AUC de 0.716 confirme que le modèle discrimine les arbres abattus nettement mieux qu'un classifieur aléatoire (AUC = 0.5). La courbe se détache clairement de la diagonale.

---

### B.4 – Odds Ratios

![Odds Ratios](figures/B5_odds_ratios.png)

Le facteur le plus discriminant est le **stade vieux** (OR = 15.2) : les arbres vieux ont 15 fois plus de risque d'être abattus qu'un arbre adulte. À l'inverse, les arbres **remarquables** sont très protégés (OR = 0.14, soit −86 % de risque) et les **jeunes** arbres rarement abattus (OR = 0.39). Chaque **diagnostic** supplémentaire multiplie le risque par 2.26, et les arbres **isolés** sont plus vulnérables que ceux en alignement (OR = 1.64). Le profil à risque est donc un arbre vieux, isolé, avec plusieurs diagnostics et non remarquable.

---

## Partie C – Zones prioritaires de plantation

L'objectif est d'identifier les quartiers où la ville devrait concentrer ses nouvelles plantations pour harmoniser le développement arboré. Un score de besoin est calculé comme suit : `score = % abattus + (100 − % jeunes) / 2`. Un score élevé signale un quartier avec beaucoup d'abattages et peu de renouvellement.

### C.1 – Répartition des stades par quartier

![Stades par quartier](figures/C1_stades_par_quartier.png)

### C.2 – Score de besoin de plantation

![Score plantation](figures/C2_score_plantation.png)

Le **Quartier de Neuville** obtient le score le plus élevé (66.7) avec seulement 10.6 % de jeunes arbres et 19.2 % d'abattus : c'est la zone la plus urgente. Le **Centre-Ville** suit (62.2) avec un taux d'abattage de 26.8 %, le plus fort de la ville. Le **Quartier de l'Europe** (57.4) est un grand quartier peu renouvelé (13 % de jeunes) offrant un fort potentiel de verdissement. À l'opposé, le **Quartier du Vermandois** affiche 70 % de jeunes arbres et 1.1 % d'abattus : aucune priorité de plantation n'y est nécessaire.

### C.3 – Âge moyen vs taux d'abattage

![Age vs abattage](figures/C3_age_vs_abattage.png)

---

## Synthèse générale

La **régression linéaire** (R² = 0.745, RMSE = 10.3 ans) montre que l'âge d'un arbre est avant tout expliqué par son diamètre de tronc et son stade de développement. La **régression logistique** (AUC = 0.716) identifie les arbres vieux, isolés et multi-diagnostiqués comme les plus à risque d'abattage. Enfin, l'analyse des zones confirme que les quartiers **Neuville** et **Centre-Ville** sont les priorités de plantation pour rééquilibrer le patrimoine arboré de Saint-Quentin.
