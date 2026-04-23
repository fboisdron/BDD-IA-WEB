library(MASS)   # chargé en premier pour éviter le masquage de dplyr::select
library(dplyr)
library(ggplot2)
library(tidyr)
library(car)
library(pROC)
library(corrplot)

INPUT_FILE  <- "../data/Patrimoine_Arboré_data_clean.csv"
FIGURES_DIR <- "figures"
dir.create(FIGURES_DIR, showWarnings = FALSE)

save_fig <- function(name, width = 10, height = 7) {
  ggsave(file.path(FIGURES_DIR, name), width = width, height = height, dpi = 150)
  cat("  ->", file.path(FIGURES_DIR, name), "\n")
}

# ---------------------------------------------------------------------------
# Chargement & préparation
# ---------------------------------------------------------------------------

df_raw <- read.csv(INPUT_FILE, stringsAsFactors = FALSE, check.names = FALSE,
                   fileEncoding = "UTF-8-BOM")

# Remplacer "N/A" (sentinelle texte) par NA réel
df_raw[df_raw == "N/A"] <- NA

df <- df_raw %>%
  mutate(
    haut_tot     = as.numeric(haut_tot),
    haut_tronc   = as.numeric(haut_tronc),
    tronc_diam   = as.numeric(tronc_diam),
    age_estim    = as.numeric(age_estim),
    clc_nbr_diag = as.numeric(clc_nbr_diag),
    Latitude     = as.numeric(Latitude),
    Longitude    = as.numeric(Longitude),
    # Cible binaire pour la régression logistique : 1 = abattu
    abattu = as.integer(fk_arb_etat == "ABATTU")
  ) %>%
  filter(!is.na(age_estim))

cat("Données chargées :", nrow(df_raw), "lignes brutes /", nrow(df), "lignes exploitables\n")

# ============================================================
# PARTIE A – RÉGRESSION LINÉAIRE : prédire age_estim
# ============================================================

cat("\n========== A. RÉGRESSION LINÉAIRE (age_estim) ==========\n")

# --- A.1 Matrice de corrélation numérique ---------------------------
num_vars <- c("age_estim", "haut_tot", "haut_tronc", "tronc_diam", "clc_nbr_diag")
mat_cor  <- cor(df[, num_vars], use = "complete.obs")
cat("\nMatrice de corrélation (Pearson) :\n")
print(round(mat_cor, 3))

png(file.path(FIGURES_DIR, "A1_corrplot.png"), width = 800, height = 700)
corrplot(mat_cor, method = "color", type = "upper", addCoef.col = "black",
         tl.col = "black", number.cex = 0.9,
         title = "Corrélations numériques", mar = c(0, 0, 2, 0))
dev.off()
cat("  ->", file.path(FIGURES_DIR, "A1_corrplot.png"), "\n")

# --- A.2 Modèle complet ---------------------------------------------
df_lm <- df %>%
  select(age_estim, haut_tot, haut_tronc, tronc_diam, clc_nbr_diag,
         fk_stadedev, fk_situation, feuillage, remarquable) %>%
  drop_na() %>%
  mutate(
    fk_stadedev  = as.factor(fk_stadedev),
    fk_situation = as.factor(fk_situation),
    feuillage    = as.factor(feuillage),
    remarquable  = as.factor(remarquable)
  )

cat("\nEffectif pour la régression linéaire :", nrow(df_lm), "observations\n")

lm_full <- lm(age_estim ~ ., data = df_lm)
cat("\n--- Modèle complet ---\n")
print(summary(lm_full))

# --- A.3 Sélection par AIC (stepwise) --------------------------------
lm_step <- stepAIC(lm_full, direction = "both", trace = FALSE)
cat("\n--- Modèle sélectionné (stepAIC) ---\n")
print(summary(lm_step))

r2   <- summary(lm_step)$r.squared
rmse <- sqrt(mean(lm_step$residuals^2))
cat(sprintf("\nR²  = %.4f\nRMSE = %.2f ans\n", r2, rmse))

# --- A.4 Diagnostics visuels ----------------------------------------
diag_df <- data.frame(
  fitted   = lm_step$fitted.values,
  residuals = lm_step$residuals
)

# Résidus vs ajustés
ggplot(diag_df, aes(fitted, residuals)) +
  geom_point(alpha = 0.15, size = 0.8, color = "steelblue") +
  geom_hline(yintercept = 0, color = "red", linetype = "dashed") +
  geom_smooth(se = FALSE, color = "orange", linewidth = 0.8) +
  labs(title = "Résidus vs Valeurs ajustées",
       x = "Valeurs ajustées", y = "Résidus") +
  theme_minimal()
save_fig("A4a_residus_vs_ajustes.png")

# Q-Q plot
ggplot(diag_df, aes(sample = residuals)) +
  stat_qq(alpha = 0.3, size = 0.8, color = "steelblue") +
  stat_qq_line(color = "red") +
  labs(title = "Q-Q plot des résidus", x = "Quantiles théoriques", y = "Quantiles observés") +
  theme_minimal()
save_fig("A4b_qqplot.png")

# --- A.5 Importance des variables (coefficients standardisés) --------
lm_coef <- summary(lm_step)$coefficients
coef_df <- data.frame(
  variable = rownames(lm_coef)[-1],
  estimate = lm_coef[-1, "Estimate"],
  pval     = lm_coef[-1, "Pr(>|t|)"]
) %>%
  mutate(significant = pval < 0.05) %>%
  arrange(desc(abs(estimate)))

cat("\nCoefficients du modèle final :\n")
print(coef_df, row.names = FALSE)

ggplot(coef_df, aes(x = reorder(variable, abs(estimate)),
                    y = estimate, fill = significant)) +
  geom_col() +
  scale_fill_manual(values = c("TRUE" = "steelblue", "FALSE" = "grey70"),
                    labels = c("TRUE" = "p < 0.05", "FALSE" = "p ≥ 0.05")) +
  coord_flip() +
  labs(title = "Coefficients du modèle de régression linéaire",
       x = NULL, y = "Coefficient estimé", fill = "Significatif") +
  theme_minimal()
save_fig("A5_coefficients.png", height = 8)

# --- A.6 Valeurs prédites vs observées --------------------------------
pred_df <- data.frame(observé = df_lm$age_estim, prédit = lm_step$fitted.values)

ggplot(pred_df, aes(observé, prédit)) +
  geom_point(alpha = 0.2, size = 0.8, color = "steelblue") +
  geom_abline(slope = 1, intercept = 0, color = "red", linetype = "dashed") +
  labs(title = "Valeurs observées vs prédites – Régression linéaire",
       x = "Âge observé (ans)", y = "Âge prédit (ans)") +
  theme_minimal()
save_fig("A6_observed_vs_predicted.png")

# ============================================================
# PARTIE B – RÉGRESSION LOGISTIQUE : prédire l'abattage
# ============================================================

cat("\n========== B. RÉGRESSION LOGISTIQUE (abattage) ==========\n")

df_log <- df %>%
  select(abattu, haut_tot, haut_tronc, tronc_diam, age_estim,
         clc_nbr_diag, fk_stadedev, fk_situation, feuillage, remarquable) %>%
  drop_na() %>%
  mutate(
    abattu       = as.factor(abattu),
    fk_stadedev  = as.factor(fk_stadedev),
    fk_situation = as.factor(fk_situation),
    feuillage    = as.factor(feuillage),
    remarquable  = as.factor(remarquable)
  )

cat("Effectif pour la régression logistique :", nrow(df_log), "observations\n")
cat("Distribution de la cible :\n")
print(table(df_log$abattu))

# --- B.1 Découpage train / test (70/30) -----------------------------
set.seed(42)
train_idx  <- sample(seq_len(nrow(df_log)), size = floor(0.7 * nrow(df_log)))
train_data <- df_log[train_idx, ]
test_data  <- df_log[-train_idx, ]

# --- B.2 Modèle logistique complet ----------------------------------
glm_full <- glm(abattu ~ ., data = train_data, family = binomial(link = "logit"))

# Sélection stepwise
glm_step <- stepAIC(glm_full, direction = "both", trace = FALSE)
cat("\n--- Modèle logistique (stepAIC) ---\n")
print(summary(glm_step))

# --- B.3 Évaluation sur le jeu de test ------------------------------
prob_test <- predict(glm_step, newdata = test_data, type = "response")
pred_test <- ifelse(prob_test > 0.5, 1, 0)

cm <- table(Prédit = pred_test, Réel = test_data$abattu)
cat("\nMatrice de confusion (seuil 0.5) :\n")
print(cm)

accuracy  <- sum(diag(cm)) / sum(cm)
precision <- cm["1", "1"] / (cm["1", "1"] + cm["1", "0"])
recall    <- cm["1", "1"] / (cm["1", "1"] + cm["0", "1"])
f1        <- 2 * precision * recall / (precision + recall)
cat(sprintf("Accuracy  : %.4f\nPrécision : %.4f\nRappel    : %.4f\nF1-score  : %.4f\n",
            accuracy, precision, recall, f1))

# --- B.4 Courbe ROC -------------------------------------------------
roc_obj <- roc(as.numeric(as.character(test_data$abattu)), prob_test, quiet = TRUE)
auc_val <- auc(roc_obj)
cat(sprintf("AUC       : %.4f\n", auc_val))

roc_df <- data.frame(
  spec = 1 - roc_obj$specificities,
  sens = roc_obj$sensitivities
)

ggplot(roc_df, aes(spec, sens)) +
  geom_line(color = "steelblue", linewidth = 1) +
  geom_abline(linetype = "dashed", color = "grey50") +
  annotate("text", x = 0.65, y = 0.15,
           label = sprintf("AUC = %.3f", auc_val), size = 5) +
  labs(title = "Courbe ROC – Régression logistique (prédiction abattage)",
       x = "1 - Spécificité", y = "Sensibilité") +
  theme_minimal()
save_fig("B4_roc_curve.png", width = 7, height = 6)

# --- B.5 Odds ratios ------------------------------------------------
or_df <- data.frame(
  variable = names(coef(glm_step))[-1],
  OR       = exp(coef(glm_step)[-1]),
  lower    = exp(confint(glm_step, level = 0.95)[-1, 1]),
  upper    = exp(confint(glm_step, level = 0.95)[-1, 2])
) %>%
  filter(abs(log(OR)) > 0.05) %>%
  arrange(desc(OR))

ggplot(or_df, aes(x = reorder(variable, OR), y = OR)) +
  geom_point(color = "steelblue", size = 2.5) +
  geom_errorbar(aes(ymin = lower, ymax = upper), width = 0.3, color = "steelblue") +
  geom_hline(yintercept = 1, linetype = "dashed", color = "red") +
  coord_flip() +
  scale_y_log10() +
  labs(title = "Odds Ratios – Régression logistique",
       x = NULL, y = "Odds Ratio (échelle log)") +
  theme_minimal()
save_fig("B5_odds_ratios.png", height = 7)

# ============================================================
# PARTIE C – ANALYSE DES ZONES DE PLANTATION
# ============================================================

cat("\n========== C. ZONES DE PLANTATION ==========\n")

zone_df <- df %>%
  filter(!is.na(clc_quartier), clc_quartier != "") %>%
  group_by(clc_quartier) %>%
  summarise(
    total_arbres = n(),
    nb_jeunes    = sum(fk_stadedev == "jeune",   na.rm = TRUE),
    nb_adultes   = sum(fk_stadedev == "adulte",  na.rm = TRUE),
    nb_vieux     = sum(fk_stadedev %in% c("vieux", "senescent"), na.rm = TRUE),
    nb_abattus   = sum(fk_arb_etat == "ABATTU",  na.rm = TRUE),
    pct_jeunes   = nb_jeunes  / total_arbres * 100,
    pct_abattus  = nb_abattus / total_arbres * 100,
    age_moyen    = mean(age_estim, na.rm = TRUE),
    .groups = "drop"
  ) %>%
  arrange(total_arbres)

cat("\nAnalyse descriptive par quartier :\n")
print(zone_df, n = Inf)

# --- C.1 Heatmap des stades par quartier ----------------------------
stade_long <- df %>%
  filter(!is.na(clc_quartier), clc_quartier != "", !is.na(fk_stadedev)) %>%
  count(clc_quartier, fk_stadedev) %>%
  group_by(clc_quartier) %>%
  mutate(pct = n / sum(n) * 100) %>%
  ungroup()

ggplot(stade_long, aes(x = fk_stadedev, y = clc_quartier, fill = pct)) +
  geom_tile(color = "white") +
  geom_text(aes(label = sprintf("%.1f%%", pct)), size = 3) +
  scale_fill_gradient(low = "white", high = "#2d6a4f") +
  labs(title = "Répartition des stades de développement par quartier (%)",
       x = "Stade", y = "Quartier", fill = "% d'arbres") +
  theme_minimal() +
  theme(axis.text.y = element_text(size = 8))
save_fig("C1_stades_par_quartier.png", height = 7)

# --- C.2 Densité arborée par quartier (nb arbres, trié) -------------
ggplot(zone_df, aes(x = reorder(clc_quartier, total_arbres), y = total_arbres,
                    fill = total_arbres)) +
  geom_col() +
  geom_hline(yintercept = mean(zone_df$total_arbres), linetype = "dashed",
             color = "red", linewidth = 0.8) +
  scale_fill_gradient(low = "#b7e4c7", high = "#1b4332") +
  coord_flip() +
  labs(title = "Nombre d'arbres par quartier",
       subtitle = "Ligne rouge = moyenne",
       x = NULL, y = "Nombre d'arbres", fill = "Effectif") +
  theme_minimal()
save_fig("C2_densite_quartier.png", height = 6)

# --- C.3 Scatter : âge moyen vs % abattus ---------------------------
p_c3 <- ggplot(zone_df, aes(x = age_moyen, y = pct_abattus, label = clc_quartier)) +
  geom_point(aes(size = total_arbres), color = "steelblue", alpha = 0.8) +
  geom_text(vjust = -0.8, size = 2.8) +
  labs(title = "Age moyen vs taux d'abattage par quartier",
       x = "Age moyen (ans)", y = "% d'arbres abattus", size = "Nb arbres") +
  theme_minimal()
print(p_c3)
save_fig("C3_age_vs_abattage.png")

cat("\nTerminé. Figures dans :", FIGURES_DIR, "\n")
