<main class="flex-grow w-full px-container-padding py-stack-lg">
    <div class="max-w-[1280px] mx-auto">
        <div class="mb-stack-lg">
            <h1 class="font-h1 text-h1 text-primary mb-2">Cartes thématiques</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant">Explorez les données du patrimoine arboré à travers deux perspectives analytiques.</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex gap-stack-md mb-stack-lg border-b border-outline-variant overflow-x-auto">
            <button class="tab-button is-active pb-3 font-label-sm text-label-sm px-stack-md border-b-2 border-primary text-primary transition-colors" data-map-tab="age">
                <span class="material-symbols-outlined text-base align-middle">calendar_today</span>
                <span>Estimation d'âge</span>
            </button>
            <button class="tab-button pb-3 font-label-sm text-label-sm px-stack-md border-b-2 border-transparent text-on-surface-variant hover:text-secondary transition-colors" data-map-tab="cluster">
                <span class="material-symbols-outlined text-base align-middle">hub</span>
                <span>Clustering de gabarit</span>
            </button>
        </div>

        <!-- Map Interface -->
        <div class="bg-white rounded-xl overflow-hidden custom-shadow border border-outline-variant">
            <!-- Map Header -->
            <div class="p-stack-md border-b border-surface-container flex justify-between items-center bg-surface-container-low">
                <div>
                    <h2 class="font-h3 text-h3 text-primary" data-map-title>Estimation d'âge</h2>
                    <p class="text-on-surface-variant font-body-md text-sm" data-map-caption>Les points sont colorés selon les classes d'âge estimé.</p>
                </div>
            </div>

            <!-- Map Container -->
            <div class="relative isolate" style="height: 600px;">
                <div class="map w-full h-full" data-map="age"></div>

                <!-- Map Overlay Controls -->
                <div class="absolute top-4 left-4 flex flex-col gap-2 z-[1001]">
                    <div class="bg-white p-3 rounded-lg shadow-md border border-outline-variant" data-map-legend></div>
                </div>

                <!-- Zoom Controls -->
                <div class="absolute bottom-4 right-4 flex flex-col gap-1 z-[1001]">
                    <button class="bg-white w-10 h-10 rounded-lg flex items-center justify-center shadow-md hover:bg-surface-container border border-outline-variant transition-colors" title="Zoom in">
                        <span class="material-symbols-outlined text-secondary">add</span>
                    </button>
                    <button class="bg-white w-10 h-10 rounded-lg flex items-center justify-center shadow-md hover:bg-surface-container border border-outline-variant transition-colors" title="Zoom out">
                        <span class="material-symbols-outlined text-secondary">remove</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Panel -->
        <div class="mt-stack-lg grid grid-cols-1 md:grid-cols-3 gap-gutter">
            <div class="bg-surface-container-low rounded-xl p-stack-md border border-outline-variant">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-secondary">info</span>
                    <h3 class="font-label-sm text-label-sm text-primary">Arbres affichés</h3>
                </div>
                <div class="text-h2 font-h2 text-primary" data-map-count>-</div>
                <p class="text-xs text-on-surface-variant mt-1">sur le registre total</p>
            </div>
            <div class="bg-surface-container-low rounded-xl p-stack-md border border-outline-variant">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-secondary">location_on</span>
                    <h3 class="font-label-sm text-label-sm text-primary">Étendue géographique</h3>
                </div>
                <div class="text-h3 font-h3 text-primary">Saint-Quentin</div>
                <p class="text-xs text-on-surface-variant mt-1">Limite administrative</p>
            </div>
            <div class="bg-surface-container-low rounded-xl p-stack-md border border-outline-variant">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-secondary">update</span>
                    <h3 class="font-label-sm text-label-sm text-primary">Dernière mise à jour</h3>
                </div>
                <div class="text-h3 font-h3 text-primary" data-map-updated>Aujourd'hui</div>
                <p class="text-xs text-on-surface-variant mt-1">Données en direct</p>
            </div>
        </div>
    </div>
</main>

<style>
    .tab-button {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tab-button.is-active {
        border-bottom-color: currentColor !important;
    }

    /* Leaflet adjustments for design system */
    .leaflet-control-zoom {
        display: none !important;
    }
</style>
