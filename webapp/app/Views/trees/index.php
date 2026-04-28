<main class="flex-grow w-full max-w-[1280px] mx-auto px-container-padding py-stack-lg">
    <div class="mb-stack-lg">
        <h1 class="font-h1 text-h1 text-primary mb-2">Catalogue des arbres</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Parcourez le registre complet des <?= number_format($total ?? 0) ?> arbres patrimoniaux enregistrés. Utilisez les filtres pour affiner votre recherche par quartier, état ou autres critères.</p>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface-container-low rounded-lg p-stack-md border border-outline-variant mb-stack-lg flex flex-col md:flex-row gap-stack-md">
        <div class="flex-grow">
            <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" placeholder="Rechercher par espèce, quartier..." data-filter="search">
        </div>
        <div class="flex gap-stack-md">
            <select class="bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all min-w-[200px]" data-filter="quartier">
                <option value="">Tous les quartiers</option>
                <option value="">-- Quartier --</option>
            </select>
            <select class="bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all min-w-[200px]" data-filter="state">
                <option value="">Tous les états</option>
                <option value="0">Bon</option>
                <option value="1">À surveiller</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl overflow-hidden custom-shadow border border-outline-variant">
        <div class="overflow-x-auto">
            <table class="w-full data-trees-table">
                <thead class="bg-primary text-on-primary sticky top-0">
                    <tr class="border-b border-outline-variant">
                        <th class="px-6 py-4 text-left font-label-sm text-label-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">label</span>
                                Espèce
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left font-label-sm text-label-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">location_on</span>
                                Localisation
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left font-label-sm text-label-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">straighten</span>
                                Dimensions
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left font-label-sm text-label-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">calendar_today</span>
                                Âge
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left font-label-sm text-label-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">health_and_safety</span>
                                État
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center font-label-sm text-label-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <?php if (!empty($trees)): ?>
                        <?php foreach ($trees as $tree): ?>
                            <tr class="hover:bg-surface-container-low transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-body-md text-primary"><?= htmlspecialchars($tree['nomfrancais'] ?? 'N/A') ?></div>
                                    <div class="text-label-sm text-on-surface-variant italic"><?= htmlspecialchars($tree['nomlatin'] ?? 'N/A') ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-body-md"><?= htmlspecialchars($tree['clc_quartier'] ?? 'N/A') ?></div>
                                    <div class="text-label-sm text-on-surface-variant"><?= htmlspecialchars($tree['clc_secteur'] ?? 'N/A') ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-body-md"><?= number_format((float)($tree['haut_tot'] ?? 0), 1) ?>m</div>
                                    <div class="text-label-sm text-on-surface-variant">Ø <?= number_format((float)($tree['tronc_diam'] ?? 0), 1) ?>cm</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-body-md"><?= number_format((float)($tree['age_estim'] ?? 0), 0) ?> ans</div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($tree['fk_arb_etat'] == 0): ?>
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary-fixed/20 text-on-secondary-fixed border border-secondary-fixed">
                                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                            <span class="font-label-sm">Bon</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-error-container text-on-error-container border border-error">
                                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">warning</span>
                                            <span class="font-label-sm">À surveiller</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-container-low border border-outline-variant hover:bg-surface-container text-primary transition-colors" title="Détails">
                                        <span class="material-symbols-outlined text-sm">info</span>
                                        <span class="hidden md:inline font-label-sm">Détails</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-4xl opacity-20">forest</span>
                                    <p class="font-body-lg">Aucun arbre trouvé</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-stack-md border-t border-outline-variant flex justify-between items-center bg-surface-container-low">
            <div class="text-on-surface-variant font-body-md">
                <?php 
                    $itemsPerPage = $itemsPerPage ?? 12;
                    $currentPage = $currentPage ?? 1;
                    $startItem = ($currentPage - 1) * $itemsPerPage + 1;
                    $endItem = min($currentPage * $itemsPerPage, $total ?? 0);
                ?>
                Affichage de <strong><?= $startItem ?></strong> à <strong><?= $endItem ?></strong> sur <strong><?= number_format($total ?? 0) ?></strong>
            </div>
            <div class="flex gap-2">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="px-4 py-2 rounded-lg bg-white border border-outline-variant hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                <?php else: ?>
                    <button disabled class="px-4 py-2 rounded-lg bg-surface-container-low border border-outline-variant opacity-50 cursor-not-allowed">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>
                <?php endif; ?>
                
                <div class="flex items-center gap-1">
                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages ?? 1, $currentPage + 2); $i++): ?>
                        <?php if ($i == $currentPage): ?>
                            <button disabled class="px-4 py-2 rounded-lg bg-primary text-white font-label-sm">
                                <?= $i ?>
                            </button>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>" class="px-4 py-2 rounded-lg bg-surface-container border border-outline-variant hover:bg-surface-container-low text-primary font-label-sm transition-colors">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <?php if ($currentPage < ($totalPages ?? 1)): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="px-4 py-2 rounded-lg bg-white border border-outline-variant hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                <?php else: ?>
                    <button disabled class="px-4 py-2 rounded-lg bg-surface-container-low border border-outline-variant opacity-50 cursor-not-allowed">
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
