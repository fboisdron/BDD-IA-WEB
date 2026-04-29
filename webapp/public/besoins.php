<?php

declare(strict_types=1);

require_once __DIR__ . '/_init.php';
$currentPage = 'besoins';
require_once __DIR__ . '/partials/header.php';
?>

<main class="flex-grow w-full max-w-[1280px] mx-auto px-container-padding py-stack-lg">
    <div class="mb-stack-lg">
        <h1 class="font-h1 text-h1 text-primary mb-2">Outils de prédiction IA</h1>
    </div>

    <!-- Prediction Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-stack-lg mb-stack-lg">
        
        <!-- Card 1: Age Prediction -->
        <div class="bg-surface-container-lowest rounded-xl signature-shadow border border-secondary-fixed overflow-hidden flex flex-col">
            <div class="p-stack-lg border-b border-secondary-fixed bg-surface-container-low">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">calendar_today</span>
                    <h2 class="font-h3 text-h3 text-primary">Estimation d'âge</h2>
                </div>
                <p class="text-on-surface-variant font-body-md text-sm">Utilise la régression pour prédire l'âge basé sur les dimensions structurelles.</p>
            </div>
            <div class="p-stack-lg flex-grow flex flex-col">
                <form class="space-y-stack-md flex-grow" data-api-form="predict-age">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Hauteur totale (m)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="haut_tot" type="number" step="0.01" required placeholder="25.5">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Hauteur tronc (m)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="haut_tronc" type="number" step="0.01" required placeholder="8.2">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Diamètre tronc (m)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="tronc_diam" type="number" step="0.01" required placeholder="1.20">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Nb diagnostics</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="clc_nbr_diag" type="number" step="1" value="0" placeholder="0">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Arbre remarquable</label>
                        <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="remarquable">
                            <option value="0">Non</option>
                            <option value="1">Oui</option>
                        </select>
                    </div>
                    <button class="w-full bg-primary-container text-on-primary py-3 rounded-lg font-label-sm signature-shadow hover:bg-secondary transition-colors active:scale-95 flex items-center justify-center gap-2 mt-auto" type="submit">
                        <span class="material-symbols-outlined">auto_awesome</span>
                        Prédire l'âge
                    </button>
                </form>
                <div class="mt-stack-md" data-form-result></div>
            </div>
        </div>

        <!-- Card 2: Clustering Prediction -->
        <div class="bg-surface-container-lowest rounded-xl signature-shadow border border-secondary-fixed overflow-hidden flex flex-col">
            <div class="p-stack-lg border-b border-secondary-fixed bg-surface-container-low">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">hub</span>
                    <h2 class="font-h3 text-h3 text-primary">Classification de gabarit</h2>
                </div>
                <p class="text-on-surface-variant font-body-md text-sm">Regroupe les arbres en catégories de taille via K-means clustering.</p>
            </div>
            <div class="p-stack-lg flex-grow flex flex-col">
                <form class="space-y-stack-md flex-grow" data-api-form="predict-cluster">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Hauteur totale (m)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="haut_tot" type="number" step="0.01" required placeholder="25.5">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Hauteur tronc (m)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="haut_tronc" type="number" step="0.01" required placeholder="8.2">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Diamètre tronc (m)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="tronc_diam" type="number" step="0.01" required placeholder="1.20">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Nombre de clusters</label>
                        <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="k">
                            <option value="2">2 catégories</option>
                            <option value="3">3 catégories</option>
                        </select>
                    </div>
                    <button class="w-full bg-primary-container text-on-primary py-3 rounded-lg font-label-sm signature-shadow hover:bg-secondary transition-colors active:scale-95 flex items-center justify-center gap-2 mt-auto" type="submit">
                        <span class="material-symbols-outlined">auto_awesome</span>
                        Classer le gabarit
                    </button>
                </form>
                <div class="mt-stack-md" data-form-result></div>
            </div>
        </div>
    </div>

    <!-- Card 3: Storm Alert (Full Width) -->
    <div class="bg-surface-container-lowest rounded-xl signature-shadow border border-secondary-fixed overflow-hidden">
        <div class="p-stack-lg border-b border-secondary-fixed bg-surface-container-low">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-error" style="font-variation-settings: 'FILL' 1;">warning</span>
                <h2 class="font-h3 text-h3 text-primary">Analyse de risque tempête</h2>
            </div>
            <p class="text-on-surface-variant font-body-md">Modèle de classification supervisée pour détecter les arbres à risque lors d'événements météorologiques extrêmes.</p>
        </div>
        <div class="p-stack-lg">
            <form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-stack-md" data-api-form="predict-alert">
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Hauteur totale (m)</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="haut_tot" type="number" step="0.01" required placeholder="25.5">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Hauteur tronc (m)</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="haut_tronc" type="number" step="0.01" required placeholder="8.2">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Diamètre tronc (m)</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="tronc_diam" type="number" step="0.01" required placeholder="1.20">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Âge estimé</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="age_estim" type="number" step="0.1" required placeholder="50">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Stade de développement</label>
                    <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_stadedev" required>
                        <option value="adulte">adulte</option>
                        <option value="jeune">jeune</option>
                        <option value="senescent">senescent</option>
                        <option value="vieux">vieux</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Port</label>
                    <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_port" required>
                        <option value="architecturé">architecturé</option>
                        <option value="couronné">couronné</option>
                        <option value="cépée">cépée</option>
                        <option value="libre">libre</option>
                        <option value="rideau">rideau</option>
                        <option value="réduit">réduit</option>
                        <option value="réduit relâché">réduit relâché</option>
                        <option value="semi libre">semi libre</option>
                        <option value="têtard">têtard</option>
                        <option value="têtard relâché">têtard relâché</option>
                        <option value="tête de chat">tête de chat</option>
                        <option value="tête de chat relâché">tête de chat relâché</option>
                        <option value="étêté">étêté</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Pied</label>
                    <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_pied" required>
                        <option value="Bac de plantation">Bac de plantation</option>
                        <option value="Revetement non permeable">Revetement non permeable</option>
                        <option value="bande de terre">bande de terre</option>
                        <option value="fosse arbre">fosse arbre</option>
                        <option value="gazon">gazon</option>
                        <option value="terre">terre</option>
                        <option value="toile tissée">toile tissée</option>
                        <option value="végétation">végétation</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Situation</label>
                    <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_situation" required>
                        <option value="Alignement">Alignement</option>
                        <option value="Groupe">Groupe</option>
                        <option value="Isolé">Isolé</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Revêtement</label>
                    <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_revetement" required>
                        <option value="Non">Non</option>
                        <option value="Oui">Oui</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Feuillage</label>
                    <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="feuillage" required>
                        <option value="Conifère">Conifère</option>
                        <option value="Feuillu">Feuillu</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-1">
                    <button class="w-full bg-error text-on-error py-3 rounded-lg font-label-sm signature-shadow hover:bg-error-container transition-colors active:scale-95 flex items-center justify-center gap-2" type="submit">
                        <span class="material-symbols-outlined">warning</span>
                        Analyser le risque
                    </button>
                </div>
            </form>
            <div class="mt-stack-lg" data-form-result></div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
