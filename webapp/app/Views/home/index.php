<!-- Hero Section -->
<section class="relative h-[600px] flex items-center overflow-hidden">
    <div class="absolute inset-0 z-0 bg-gradient-to-br from-primary via-secondary to-primary-container opacity-90"></div>
    <div class="container mx-auto px-container-padding relative z-10 max-w-[1280px]">
        <div class="max-w-2xl text-white">
            <h1 class="font-h1 text-h1 mb-stack-md text-secondary-fixed">Préservation du patrimoine arboré de Saint-Quentin</h1>
            <p class="font-body-lg text-body-lg mb-stack-lg text-on-primary-container leading-relaxed">
                Une initiative dédiée de la Ville de Saint-Quentin pour documenter, surveiller et protéger les arbres patrimoniaux de notre ville. Rejoignez-nous dans la gestion de notre forêt urbaine, garantissant un héritage plus verdoyant pour les générations à venir.
            </p>
            <div class="flex flex-wrap gap-stack-md">
                <a href="/maps" class="bg-primary-container text-white px-8 py-3 rounded-lg font-label-sm custom-shadow flex items-center gap-2 hover:bg-secondary transition-colors">
                    <span class="material-symbols-outlined">explore</span>
                    Explorer le registre
                </a>
                <a href="/predictions" class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-8 py-3 rounded-lg font-label-sm flex items-center gap-2 hover:bg-white/20 transition-colors">
                    <span class="material-symbols-outlined">info</span>
                    Outils IA
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Bento Grid Dashboard Preview -->
<section class="py-24 bg-surface-container-low">
    <div class="container mx-auto px-container-padding max-w-[1280px]">
        <div class="flex flex-col md:flex-row justify-between items-end mb-stack-lg">
            <div class="max-w-xl">
                <span class="text-secondary font-label-sm tracking-widest uppercase mb-unit block">Analyse en temps réel</span>
                <h2 class="font-h2 text-h2 text-primary">Inventaire de la forêt urbaine</h2>
            </div>
            <a href="/maps" class="text-secondary font-label-sm flex items-center gap-1 border-b border-secondary pb-1 hover:text-primary transition-colors">
                Voir le tableau de bord <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 grid-rows-2 gap-gutter h-auto md:h-[500px]">
            <!-- Large Feature Card -->
            <div class="md:col-span-2 md:row-span-2 bg-white rounded-xl p-stack-lg custom-shadow flex flex-col border border-emerald-100">
                <div class="flex items-center gap-2 text-secondary mb-stack-md">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">park</span>
                    <span class="font-label-sm">Couverture de canopée patrimoniale</span>
                </div>
                <div class="flex-grow flex items-end">
                    <div class="w-full">
                        <div class="text-h1 font-h1 text-primary-container mb-unit" data-summary="total"><?= number_format($summary['total'] ?? 0) ?></div>
                        <div class="text-on-surface-variant font-body-md mb-stack-md">Arbres patrimoniaux enregistrés</div>
                        <div class="h-4 bg-surface-container rounded-full overflow-hidden">
                            <div class="h-full bg-secondary w-3/4 rounded-full"></div>
                        </div>
                        <p class="text-label-sm text-outline mt-2 italic">Objectif: 1 600 d'ici 2025</p>
                    </div>
                </div>
            </div>
            
            <!-- Medium Card: Map Preview -->
            <div class="md:col-span-2 bg-primary-container rounded-xl relative overflow-hidden custom-shadow group">
                <div class="absolute inset-0 opacity-40 bg-gradient-to-br from-primary to-secondary"></div>
                <div class="relative z-10 p-stack-lg h-full flex flex-col justify-between">
                    <h3 class="text-white font-h3">Cartographie interactive</h3>
                    <a href="/maps" class="bg-secondary-fixed text-on-secondary-fixed w-fit px-4 py-2 rounded-full font-label-sm flex items-center gap-2 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined">map</span>
                        Lancer l'explorateur de cartes
                    </a>
                </div>
            </div>
            
            <!-- Small Stat Card 1 -->
            <div class="bg-white rounded-xl p-stack-md flex flex-col justify-between border border-emerald-100 custom-shadow">
                <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">health_and_safety</span>
                <div>
                    <div class="text-h3 font-h3 text-primary"><?= number_format($summary['remarkable'] ?? 0) ?></div>
                    <div class="text-label-sm text-on-surface-variant">Arbres remarquables</div>
                </div>
            </div>
            
            <!-- Small Stat Card 2 -->
            <div class="bg-white rounded-xl p-stack-md flex flex-col justify-between border border-emerald-100 custom-shadow">
                <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">eco</span>
                <div>
                    <div class="text-h3 font-h3 text-primary"><?= number_format((float)($summary['avg_age'] ?? 0), 1) ?></div>
                    <div class="text-label-sm text-on-surface-variant">Âge moyen estimé</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Institutional Description -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-container-padding max-w-[1280px]">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
            <div class="relative">
                <div class="rounded-2xl custom-shadow w-full aspect-square bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[120px] text-secondary opacity-30">landscape</span>
                </div>
                <div class="absolute -bottom-8 -right-8 bg-tertiary-container text-on-tertiary-container p-stack-lg rounded-xl custom-shadow max-w-[240px]">
                    <p class="text-label-sm font-bold uppercase tracking-widest mb-2">Mandat municipal</p>
                    <p class="text-body-md leading-snug">« Protéger les poumons de notre ville n'est pas seulement de l'environnementalisme; c'est notre devoir civique. »</p>
                </div>
            </div>
            <div class="space-y-stack-md">
                <h2 class="font-h2 text-h2 text-primary">Un patrimoine enraciné dans l'histoire</h2>
                <p class="font-body-lg text-body-lg text-on-surface-variant">
                    Le projet Patrimoine Arboré de Saint-Quentin est un cadre complet établi pour identifier et sauvegarder des arbres individuels d'une valeur botanique, historique ou esthétique importante.
                </p>
                <div class="grid grid-cols-1 gap-6 pt-stack-md">
                    <div class="flex gap-4 p-stack-md bg-surface-container-low rounded-lg border border-outline-variant">
                        <span class="material-symbols-outlined text-secondary-container bg-primary-container p-3 rounded-lg h-fit">verified</span>
                        <div>
                            <h4 class="font-h3 text-label-sm text-primary">Évaluation botanique</h4>
                            <p class="text-label-sm text-on-surface-variant">Vérifications sanitaires rigoureuses et classification par arboriculteurs municipaux agréés.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 p-stack-md bg-surface-container-low rounded-lg border border-outline-variant">
                        <span class="material-symbols-outlined text-secondary-container bg-primary-container p-3 rounded-lg h-fit">history_edu</span>
                        <div>
                            <h4 class="font-h3 text-label-sm text-primary">Cartographie historique</h4>
                            <p class="text-label-sm text-on-surface-variant">Traçage de l'impact culturel de la foresterie urbaine à travers les archives municipales.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 p-stack-md bg-surface-container-low rounded-lg border border-outline-variant">
                        <span class="material-symbols-outlined text-secondary-container bg-primary-container p-3 rounded-lg h-fit">security</span>
                        <div>
                            <h4 class="font-h3 text-label-sm text-primary">Protection légale</h4>
                            <p class="text-label-sm text-on-surface-variant">Application du zonage environnemental et des lois municipales de conservation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 bg-primary text-white text-center">
    <div class="container mx-auto px-container-padding max-w-2xl">
        <span class="material-symbols-outlined text-6xl mb-stack-md text-secondary-fixed-dim block" style="font-variation-settings: 'FILL' 1;">landscape</span>
        <h2 class="font-h1 text-h1 mb-stack-md">Prêt à contribuer?</h2>
        <p class="font-body-lg text-on-primary-container mb-stack-lg">
            Que vous soyez arboriste, chercheur ou citoyen engagé, votre participation nous aide à construire une canopée urbaine plus résiliente.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-stack-md">
            <a href="/trees/create" class="bg-secondary-fixed text-on-secondary-fixed px-10 py-4 rounded-lg font-h3 custom-shadow hover:bg-secondary-container transition-colors">
                Devenir sentinelle
            </a>
            <button class="bg-transparent border-2 border-white/30 text-white px-10 py-4 rounded-lg font-h3 hover:bg-white/10 transition-colors">
                Contacter le bureau d'arboriculture
            </button>
        </div>
    </div>
</section>
