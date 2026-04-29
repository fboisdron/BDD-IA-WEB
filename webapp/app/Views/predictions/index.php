<main class="flex-grow w-full max-w-[1280px] mx-auto px-container-padding py-stack-lg">
    <div class="mb-stack-lg">
        <h1 class="font-h1 text-h1 text-primary mb-2">Outils de prédiction IA</h1>

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
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_stadedev" required placeholder="Mature">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Port</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_port" required placeholder="Pyramidal">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Pied</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_pied" required placeholder="Libre">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Situation</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_situation" required placeholder="Plein air">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Revêtement</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_revetement" required placeholder="Bitume">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Feuillage</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="feuillage" required placeholder="Caduque">
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
