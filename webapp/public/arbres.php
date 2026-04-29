<?php

declare(strict_types=1);

require_once __DIR__ . '/_init.php';
$currentPage = 'arbres';
require_once __DIR__ . '/partials/header.php';
?>

<main class="flex-grow w-full px-container-padding py-stack-lg">
    <div class="max-w-[1280px] mx-auto">

        <div class="mb-stack-lg">
            <h1 class="font-h1 text-h1 text-primary mb-2">Catalogue des arbres</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant">Consultez et filtrez l'ensemble du patrimoine arboré de Saint-Quentin.</p>
        </div>

        <!-- Filters -->
        <div class="bg-surface-container-low rounded-xl border border-outline-variant p-stack-md mb-stack-lg">
            <!-- Text filters -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Recherche</label>
                    <input id="f-q" type="text" placeholder="Nom, quartier, secteur…"
                        class="w-full rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Quartier</label>
                    <input id="f-quartier" type="text" placeholder="Ex : Centre-ville"
                        class="w-full rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Secteur</label>
                    <input id="f-secteur" type="text" placeholder="Ex : Nord"
                        class="w-full rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                </div>
            </div>
            <!-- Enum filters -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-4">
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Stade</label>
                    <select id="f-stade" class="w-full rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="">Tous</option>
                        <option value="adulte">Adulte</option>
                        <option value="jeune">Jeune</option>
                        <option value="senescent">Sénescent</option>
                        <option value="vieux">Vieux</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Port</label>
                    <select id="f-port" class="w-full rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="">Tous</option>
                        <option value="architecturé">Architecturé</option>
                        <option value="couronné">Couronné</option>
                        <option value="cépée">Cépée</option>
                        <option value="libre">Libre</option>
                        <option value="rideau">Rideau</option>
                        <option value="réduit">Réduit</option>
                        <option value="réduit relâché">Réduit relâché</option>
                        <option value="semi libre">Semi libre</option>
                        <option value="têtard">Têtard</option>
                        <option value="têtard relâché">Têtard relâché</option>
                        <option value="tête de chat">Tête de chat</option>
                        <option value="tête de chat relâché">Tête de chat relâché</option>
                        <option value="étêté">Étêté</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Pied</label>
                    <select id="f-pied" class="w-full rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="">Tous</option>
                        <option value="Bac de plantation">Bac de plantation</option>
                        <option value="Revetement non permeable">Revêtement non perméable</option>
                        <option value="bande de terre">Bande de terre</option>
                        <option value="fosse arbre">Fosse arbre</option>
                        <option value="gazon">Gazon</option>
                        <option value="terre">Terre</option>
                        <option value="toile tissée">Toile tissée</option>
                        <option value="végétation">Végétation</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Situation</label>
                    <select id="f-situation" class="w-full rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="">Tous</option>
                        <option value="Alignement">Alignement</option>
                        <option value="Groupe">Groupe</option>
                        <option value="Isolé">Isolé</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Revêtement</label>
                    <select id="f-revetement" class="w-full rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="">Tous</option>
                        <option value="Oui">Oui</option>
                        <option value="Non">Non</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Feuillage</label>
                    <select id="f-feuillage" class="w-full rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="">Tous</option>
                        <option value="Conifère">Conifère</option>
                        <option value="Feuillu">Feuillu</option>
                        <option value="N/A">N/A</option>
                    </select>
                </div>
            </div>
            <!-- Controls row -->
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Remarquable</label>
                    <select id="f-remarquable"
                        class="rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="">Tous</option>
                        <option value="1">Oui</option>
                        <option value="0">Non</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Résultats par page</label>
                    <select id="f-limit"
                        class="rounded-lg border border-outline-variant bg-white pl-3 pr-8 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <button id="btn-reset"
                    class="px-4 py-2 rounded-lg border border-outline-variant text-sm text-on-surface-variant hover:bg-surface-container transition-colors">
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-outline-variant overflow-hidden custom-shadow">
            <!-- Table header bar -->
            <div class="flex items-center justify-between px-6 py-3 bg-surface-container-low border-b border-outline-variant">
                <span id="result-count" class="font-label-sm text-label-sm text-on-surface-variant">Chargement…</span>
                <div id="pagination-top" class="flex items-center gap-1"></div>
            </div>

            <!-- Scrollable table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm table-fixed">
                    <colgroup>
                        <col class="w-16">       <!-- ID -->
                        <col class="w-[15%]">    <!-- Nom français -->
                        <col class="w-[15%]">    <!-- Nom latin -->
                        <col class="w-[12%]">    <!-- Quartier -->
                        <col class="w-[10%]">    <!-- Secteur -->
                        <col class="w-[9%]">     <!-- Stade -->
                        <col class="w-[8%]">     <!-- Haut. -->
                        <col class="w-[8%]">     <!-- Diam. -->
                        <col class="w-[8%]">     <!-- Âge est. -->
                        <col class="w-[9%]">     <!-- Feuillage -->
                        <col class="w-[9%]">     <!-- Remarquable -->
                    </colgroup>
                    <thead>
                        <tr class="bg-primary text-white font-label-sm uppercase tracking-widest text-[11px]">
                            <th class="px-4 py-3 cursor-pointer select-none hover:bg-primary-container transition-colors" data-sort="id_arbre">
                                <span class="flex items-center gap-1">ID <span class="sort-icon opacity-50">⇅</span></span>
                            </th>
                            <th class="px-4 py-3 truncate">Nom français</th>
                            <th class="px-4 py-3 truncate">Nom latin</th>
                            <th class="px-4 py-3 truncate">Quartier</th>
                            <th class="px-4 py-3 truncate">Secteur</th>
                            <th class="px-4 py-3 truncate">Stade</th>
                            <th class="px-4 py-3 cursor-pointer select-none hover:bg-primary-container transition-colors" data-sort="haut_tot">
                                <span class="flex items-center gap-1">Haut. (m) <span class="sort-icon opacity-50">⇅</span></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none hover:bg-primary-container transition-colors" data-sort="tronc_diam">
                                <span class="flex items-center gap-1">Diam. (m) <span class="sort-icon opacity-50">⇅</span></span>
                            </th>
                            <th class="px-4 py-3 cursor-pointer select-none hover:bg-primary-container transition-colors" data-sort="age_estim">
                                <span class="flex items-center gap-1">Âge est. <span class="sort-icon opacity-50">⇅</span></span>
                            </th>
                            <th class="px-4 py-3 truncate">Feuillage</th>
                            <th class="px-4 py-3 truncate">Remarquable</th>
                        </tr>
                    </thead>
                    <tbody id="tree-tbody" class="divide-y divide-surface-container text-on-surface-variant">
                        <tr><td colspan="11" class="px-6 py-8 text-center text-on-surface-variant">Chargement…</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Bottom pagination -->
            <div class="flex items-center justify-between px-6 py-3 border-t border-outline-variant bg-surface-container-low">
                <span id="page-info" class="font-label-sm text-label-sm text-on-surface-variant"></span>
                <div id="pagination-bottom" class="flex items-center gap-1"></div>
            </div>
        </div>

    </div>
</main>

<script>
(() => {
    const apiUrl = document.querySelector('meta[name="api-url"]')?.content ?? 'api.php';

    let currentPage = 1;
    let debounceTimer = null;
    let sortCol = null;
    let sortOrder = null;

    const filters = () => ({
        q:           document.getElementById('f-q').value.trim(),
        quartier:    document.getElementById('f-quartier').value.trim(),
        secteur:     document.getElementById('f-secteur').value.trim(),
        stade:       document.getElementById('f-stade').value,
        port:        document.getElementById('f-port').value,
        pied:        document.getElementById('f-pied').value,
        situation:   document.getElementById('f-situation').value,
        revetement:  document.getElementById('f-revetement').value,
        feuillage:   document.getElementById('f-feuillage').value,
        remarquable: document.getElementById('f-remarquable').value,
        limit:       document.getElementById('f-limit').value,
    });

    const buildUrl = (page) => {
        const f = filters();
        const params = new URLSearchParams({ action: 'trees', page, ...f });
        if (sortCol) { params.set('sort', sortCol); params.set('order', sortOrder); }
        return `${apiUrl}?${params}`;
    };

    const updateSortHeaders = () => {
        document.querySelectorAll('[data-sort]').forEach(th => {
            const col = th.dataset.sort;
            const icon = th.querySelector('.sort-icon');
            if (col === sortCol) {
                icon.textContent = sortOrder === 'ASC' ? '↑' : '↓';
                icon.classList.remove('opacity-50');
                th.classList.add('bg-primary-container');
            } else {
                icon.textContent = '⇅';
                icon.classList.add('opacity-50');
                th.classList.remove('bg-primary-container');
            }
        });
    };

    const renderRow = (item, idx) => {
        const rem = Number(item.remarquable) === 1;
        return `
            <tr class="${idx % 2 === 0 ? 'bg-white' : 'bg-surface-container-low'} hover:bg-surface-container transition-colors">
                <td class="px-4 py-3 font-semibold text-primary">${item.id_arbre ?? '—'}</td>
                <td class="px-4 py-3 truncate max-w-0" title="${item.nomfrancais ?? ''}">${item.nomfrancais ?? '—'}</td>
                <td class="px-4 py-3 truncate max-w-0 italic text-xs" title="${item.nomlatin ?? ''}">${item.nomlatin ?? '—'}</td>
                <td class="px-4 py-3 truncate max-w-0" title="${item.clc_quartier ?? ''}">${item.clc_quartier ?? '—'}</td>
                <td class="px-4 py-3 truncate max-w-0" title="${item.clc_secteur ?? ''}">${item.clc_secteur ?? '—'}</td>
                <td class="px-4 py-3 truncate max-w-0">${item.fk_stadedev ?? '—'}</td>
                <td class="px-4 py-3">${item.haut_tot != null ? Number(item.haut_tot).toFixed(1) : '—'}</td>
                <td class="px-4 py-3">${item.tronc_diam != null ? Number(item.tronc_diam).toFixed(2) : '—'}</td>
                <td class="px-4 py-3">${item.age_estim != null ? Math.round(item.age_estim) + ' ans' : '—'}</td>
                <td class="px-4 py-3 truncate max-w-0">${item.feuillage ?? '—'}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold uppercase
                        ${rem ? 'bg-secondary-fixed text-on-secondary-fixed-variant' : 'bg-surface-container text-on-surface-variant'}">
                        ${rem ? 'Oui' : 'Non'}
                    </span>
                </td>
            </tr>`;
    };

    const renderPagination = (pagination, containerId) => {
        const { page, pages } = pagination;
        const container = document.getElementById(containerId);
        if (!container) return;

        const btn = (label, p, disabled = false, active = false) =>
            `<button data-page="${p}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                    ${active ? 'bg-primary text-white border-primary' : 'bg-white text-on-surface-variant border-outline-variant hover:bg-surface-container'}
                    ${disabled ? 'opacity-40 cursor-not-allowed pointer-events-none' : ''}"
                ${disabled ? 'disabled' : ''}>
                ${label}
            </button>`;

        let html = btn('‹', page - 1, page <= 1);

        const delta = 2;
        const range = [];
        for (let i = Math.max(1, page - delta); i <= Math.min(pages, page + delta); i++) range.push(i);
        if (range[0] > 1) { html += btn('1', 1); if (range[0] > 2) html += `<span class="px-1 text-on-surface-variant text-xs">…</span>`; }
        range.forEach(p => { html += btn(p, p, false, p === page); });
        if (range[range.length - 1] < pages) { if (range[range.length - 1] < pages - 1) html += `<span class="px-1 text-on-surface-variant text-xs">…</span>`; html += btn(pages, pages); }

        html += btn('›', page + 1, page >= pages);
        container.innerHTML = html;
    };

    const load = async (page = 1) => {
        currentPage = page;
        const tbody = document.getElementById('tree-tbody');
        tbody.style.opacity = '0.4';
        tbody.style.pointerEvents = 'none';

        try {
            const res = await fetch(buildUrl(page));
            const data = await res.json();
            if (!data.ok) throw new Error(data.error ?? 'Erreur serveur');

            const { items, pagination } = data.data;

            tbody.style.opacity = '';
            tbody.style.pointerEvents = '';
            tbody.innerHTML = items.length
                ? items.map((item, idx) => renderRow(item, idx)).join('')
                : `<tr><td colspan="11" class="px-6 py-8 text-center text-on-surface-variant">Aucun arbre trouvé.</td></tr>`;

            const { page: p, pages, total, limit } = pagination;
            const from = (p - 1) * limit + 1;
            const to = Math.min(p * limit, total);
            document.getElementById('result-count').textContent =
                total === 0 ? 'Aucun résultat' : `${from}–${to} sur ${total} arbre${total > 1 ? 's' : ''}`;
            document.getElementById('page-info').textContent = `Page ${p} / ${pages}`;

            renderPagination(pagination, 'pagination-top');
            renderPagination(pagination, 'pagination-bottom');

            document.querySelectorAll('[data-page]').forEach(btn => {
                btn.addEventListener('click', () => load(Number(btn.dataset.page)));
            });
        } catch (err) {
            tbody.style.opacity = '';
            tbody.style.pointerEvents = '';
            tbody.innerHTML = `<tr><td colspan="11" class="px-6 py-8 text-center text-error">${err.message}</td></tr>`;
        }
    };

    const resetFilters = () => {
        ['f-q', 'f-quartier', 'f-secteur'].forEach(id => document.getElementById(id).value = '');
        ['f-stade', 'f-port', 'f-pied', 'f-situation', 'f-revetement', 'f-feuillage', 'f-remarquable'].forEach(id =>
            document.getElementById(id).value = '');
        document.getElementById('f-limit').value = '25';
        sortCol = null; sortOrder = null;
        updateSortHeaders();
        load(1);
    };

    const debouncedLoad = () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => load(1), 350);
    };

    ['f-q', 'f-quartier', 'f-secteur'].forEach(id =>
        document.getElementById(id).addEventListener('input', debouncedLoad));
    ['f-stade', 'f-port', 'f-pied', 'f-situation', 'f-revetement', 'f-feuillage', 'f-remarquable', 'f-limit'].forEach(id =>
        document.getElementById(id).addEventListener('change', () => load(1)));
    document.getElementById('btn-reset').addEventListener('click', resetFilters);

    document.querySelectorAll('[data-sort]').forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.sort;
            if (sortCol !== col) { sortCol = col; sortOrder = 'ASC'; }
            else if (sortOrder === 'ASC') { sortOrder = 'DESC'; }
            else { sortCol = null; sortOrder = null; }
            updateSortHeaders();
            load(1);
        });
    });

    load(1);
})();
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
