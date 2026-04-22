# Fonctionnalité 2 – Visualisation des données sur des graphiques

**Jeu de données :** `Patrimoine_Arboré_data_clean.csv` — 11 418 arbres, 23 variables  
**Figures générées dans :** `../data/figures/`

---

## Figure 1 – Répartition des arbres par stade de développement

![Stade de développement](../data/figures/fig1_stade_developpement.png)

| Stade | Effectif | Part |
|---|---|---|
| adulte | 6 473 | 57.7 % |
| jeune | 4 620 | 41.2 % |
| vieux | 68 | 0.6 % |
| sénescent | 53 | 0.5 % |

**Interprétation :** Le patrimoine arboré de Saint-Quentin est majoritairement composé d'arbres adultes (57.7 %) et jeunes (41.2 %). Les arbres vieux et sénescents sont très minoritaires (moins de 1.2 % combinés), ce qui témoigne d'un renouvellement régulier du parc arboré et d'une politique de plantation active. Ce résultat est cohérent avec la vocation urbaine du patrimoine (alignements, parcs publics) où les arbres sont replantés fréquemment.

---

## Figure 2 – Nombre d'arbres par quartier

![Arbres par quartier](../data/figures/fig2_arbres_par_quartier.png)

| Quartier | Effectif |
|---|---|
| Quartier Saint-Martin - Oëstres | 2 009 |
| Quartier Remicourt | 1 765 |
| Quartier du faubourg d'Isle | 1 715 |
| Quartier de l'Europe | 1 428 |
| Quartier du Vermandois | 1 416 |
| Quartier Saint-Jean | 746 |
| Quartier du Centre-Ville | 740 |
| OMISSY | 642 |
| Quartier de Neuville | 302 |
| HARLY | 156 |
| ROUVROY | 6 |

**Interprétation :** La densité arborée est très inégalement répartie. Les quartiers périphériques (Saint-Martin - Oëstres, Remicourt, faubourg d'Isle) concentrent le plus grand nombre d'arbres, probablement grâce à la présence de parcs et d'espaces verts étendus. Le Centre-Ville, plus minéralisé, affiche un patrimoine plus modeste (740 arbres). Les communes annexes ROUVROY et HARLY restent très peu représentées dans le jeu de données.

---

## Figure 3 – Répartition des arbres par situation

![Arbres par situation](../data/figures/fig3_arbres_par_situation.png)

| Situation | Effectif |
|---|---|
| Alignement | 6 555 (57.4 %) |
| Groupe | 3 812 (33.4 %) |
| Isolé | 1 033 (9.0 %) |

**Interprétation :** La majorité des arbres se trouvent en alignement (bords de routes, trottoirs), ce qui est typique d'un patrimoine urbain géré. Les arbres en groupe (parcs, massifs) représentent un tiers du total. Les arbres isolés, souvent des sujets remarquables ou décoratifs, restent peu nombreux.

---

## Figure 4 – Distribution de l'âge estimé

![Distribution de l'âge](../data/figures/fig4_distribution_age.png)

| Statistique | Valeur |
|---|---|
| Moyenne | 29.4 ans |
| Médiane | 30.0 ans |
| Écart-type | 20.5 ans |
| Min | 0 ans |
| Max | 200 ans |
| Q1 / Q3 | 15 / 40 ans |

**Interprétation :** La distribution de l'âge est légèrement asymétrique à droite avec un pic autour de 15–40 ans, confirmant que la majorité du parc arboré est relativement jeune. La queue droite (quelques arbres dépassant 100–200 ans) correspond aux arbres remarquables ou historiques. La médiane à 30 ans et la moyenne à 29.4 ans sont très proches, signe d'une distribution relativement équilibrée malgré les valeurs extrêmes.

---

## Figure 5 – Hauteur totale par stade de développement

![Hauteur par stade](../data/figures/fig5_hauteur_par_stade.png)

| Stade | Médiane | Moyenne |
|---|---|---|
| jeune | 6.0 m | 6.7 m |
| adulte | 12.0 m | 12.8 m |
| vieux | 24.0 m | 23.5 m |
| sénescent | 22.0 m | 22.7 m |

**Interprétation :** La hauteur augmente logiquement avec le stade de développement, passant de 6 m pour les jeunes arbres à plus de 22 m pour les vieux et sénescents. La faible différence entre vieux et sénescent s'explique par le ralentissement de la croissance en hauteur à la fin du cycle de vie — l'arbre grossit davantage en diamètre qu'il ne grandit.

---

## Figure 6 – Top 20 secteurs par nombre d'arbres

![Top 20 secteurs](../data/figures/fig6_secteurs_top20.png)

| Secteur | Effectif |
|---|---|
| Parc des Champs-Élysées | 591 |
| Parc d'Isle Jacques Braconnier | 454 |
| Rue Georges Charpak | 435 |
| Terrain aventure Europe | 432 |
| Rue André Missenard | 370 |

**Interprétation :** Les parcs publics dominent le classement : les deux premiers secteurs sont des espaces verts structurants de la ville. Les voies à fort alignement (rues Georges Charpak, André Missenard) illustrent la politique d'alignement arboré sur les axes principaux. Sur les 308 secteurs recensés, le top 20 concentre à lui seul une part significative du patrimoine total.

---

## Figure 7 – Diamètre du tronc par type d'arbre (top 20)

![Tronc par type](../data/figures/fig7_tronc_par_type.png)

**Interprétation :** Ce boxplot horizontal révèle la variabilité intra-espèce du diamètre de tronc. Les espèces à fort développement (platanes, chênes, tilleuls) présentent des troncs plus épais et une dispersion plus grande, témoignant d'une grande hétérogénéité d'âge au sein d'une même espèce. Les espèces ornementales plus récentes affichent des diamètres uniformément faibles.

---

## Figure 8 – Arbres remarquables par quartier

![Remarquables par quartier](../data/figures/fig8_remarquables_quartier.png)

| Quartier | Arbres remarquables |
|---|---|
| Quartier Remicourt | 99 |
| Quartier du Centre-Ville | 5 |
| Quartier du faubourg d'Isle | 4 |
| **Total** | **108** |

**Interprétation :** Sur 11 418 arbres, seulement **108 sont classés remarquables** (< 1 %). Ils sont très concentrés dans le Quartier Remicourt (91.7 % du total remarquable), qui abrite vraisemblablement un parc ou un ensemble historique de grande valeur patrimoniale. Cette concentration géographique est un résultat fort : la gestion des arbres remarquables est essentiellement localisée dans un seul quartier.

---

## Synthèse

| # | Figure | Observation clé |
|---|---|---|
| 1 | Stade de développement | 99 % des arbres sont jeunes ou adultes |
| 2 | Arbres par quartier | Saint-Martin - Oëstres est le quartier le plus arboré (2 009 arbres) |
| 3 | Situation | 57 % des arbres sont en alignement |
| 4 | Distribution de l'âge | Médiane à 30 ans, quelques arbres > 100 ans |
| 5 | Hauteur par stade | Progression de 6 m (jeune) à 24 m (vieux) |
| 6 | Top 20 secteurs | Les parcs publics dominent ; 2 parcs dans le top 2 |
| 7 | Diamètre par espèce | Grande variabilité intra-espèce pour les essences matures |
| 8 | Arbres remarquables | 108 arbres remarquables, 92 % concentrés à Remicourt |
