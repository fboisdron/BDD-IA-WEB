(function () {
    const apiUrl = 'api.php';

    const json = async (response) => {
        const payload = await response.json();
        if (!response.ok || payload.ok === false) {
            throw new Error(payload.error || 'Une erreur est survenue.');
        }
        return payload.data;
    };

    const formatNumber = (value, digits = 0) => {
        if (value === null || value === undefined || Number.isNaN(Number(value))) {
            return '-';
        }
        return Number(value).toLocaleString('fr-FR', {
            maximumFractionDigits: digits,
            minimumFractionDigits: digits,
        });
    };

    const fillSummary = async () => {
        const summaryTargets = document.querySelectorAll('[data-summary]');
        if (!summaryTargets.length) {
            return;
        }

        try {
            const data = await json(await fetch(`${apiUrl}?action=summary`));
            const summary = data.summary;

            summaryTargets.forEach((node) => {
                const key = node.getAttribute('data-summary');
                if (key === 'avg_height') {
                    node.textContent = `${formatNumber(summary.avg_height, 2)} m`;
                } else if (key === 'avg_age') {
                    node.textContent = `${formatNumber(summary.avg_age, 1)} ans`;
                } else {
                    node.textContent = formatNumber(summary[key]);
                }
            });

            const quartierList = document.querySelector('[data-summary-list="quartier"]');
            if (quartierList) {
                quartierList.innerHTML = data.by_quartier.map((item) => `
                    <div class="chart-row">
                        <div>
                            <strong>${item.clc_quartier}</strong><br>
                            <span>${formatNumber(item.total)} arbres</span>
                        </div>
                        <div class="chart-bar" style="width:${Math.min(100, Math.max(12, Number(item.total) / 8))}%"></div>
                    </div>
                `).join('');
            }

            const stadeList = document.querySelector('[data-summary-list="stade"]');
            if (stadeList) {
                stadeList.innerHTML = data.by_stade.map((item) => `
                    <div class="chart-row">
                        <div>
                            <strong>${item.fk_stadedev}</strong><br>
                            <span>${formatNumber(item.total)} arbres</span>
                        </div>
                        <div class="chart-bar" style="width:${Math.min(100, Math.max(12, Number(item.total) / 8))}%"></div>
                    </div>
                `).join('');
            }
        } catch (error) {
            summaryTargets.forEach((node) => {
                node.textContent = 'N/A';
            });
        }
    };

    const fillTreesTable = async () => {
        const holder = document.querySelector('[data-tree-table]');
        if (!holder) {
            return;
        }

        try {
            const data = await json(await fetch(`${apiUrl}?action=trees&limit=12`));
            holder.innerHTML = `
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-primary text-white font-label-sm uppercase tracking-widest text-[12px]">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Quartier</th>
                            <th class="px-6 py-4">Secteur</th>
                            <th class="px-6 py-4">Espèce</th>
                            <th class="px-6 py-4">Âge</th>
                            <th class="px-6 py-4">Remarquable</th>
                        </tr>
                    </thead>
                    <tbody class="text-body-md text-on-surface-variant divide-y divide-surface-container">
                        ${data.items.map((item, idx) => `
                            <tr class="${idx % 2 === 0 ? '' : 'bg-surface-container-lowest'} hover:bg-surface-container-low transition-colors">
                                <td class="px-6 py-4 font-semibold text-primary">${item.id_arbre ?? ''}</td>
                                <td class="px-6 py-4">${item.clc_quartier ?? ''}</td>
                                <td class="px-6 py-4">${item.clc_secteur ?? ''}</td>
                                <td class="px-6 py-4 font-semibold">${item.fk_nomtech ?? item.nomfrancais ?? ''}</td>
                                <td class="px-6 py-4">${formatNumber(item.age_estim, 1)} ans</td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase ${Number(item.remarquable) === 1 ? 'bg-secondary-fixed text-on-secondary-fixed-variant' : 'bg-surface-container text-on-surface-variant'}">
                                        ${Number(item.remarquable) === 1 ? 'Oui' : 'Non'}
                                    </span>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        } catch (error) {
            holder.innerHTML = `<div class="p-stack-lg bg-error-container/20 border border-error rounded-lg text-error">Impossible de charger le catalogue.</div>`;
        }
    };

    const serializeForm = (form) => Object.fromEntries(new FormData(form).entries());

    const bindForms = () => {
        document.querySelectorAll('[data-api-form]').forEach((form) => {
            let result = form.querySelector('[data-form-result]');
            if (!result) {
                result = form.closest('[data-form-result]');
            }
            if (!result) {
                result = form.parentElement.querySelector('[data-form-result]');
            }
            
            form.addEventListener('submit', async (event) => {
                const nomlatinInput = form.querySelector('input[name="nomlatin"]');
                const fkNomtechInput = form.querySelector('input[name="fk_nomtech"]');
                if (nomlatinInput && fkNomtechInput) {
                    fkNomtechInput.value = nomlatinInput.value;
                }
                event.preventDefault();
                // Use HTML5 constraint validation to prevent empty/invalid inputs
                if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                    if (typeof form.reportValidity === 'function') {
                        form.reportValidity();
                    }
                    return;
                }
                if (result) {
                    result.innerHTML = '<div class="mt-stack-md p-stack-md bg-surface-container rounded-lg text-on-surface-variant">Traitement en cours...</div>';
                }

                const action = form.getAttribute('data-api-form');

                try {
                    const payload = serializeForm(form);
                    const response = await fetch(`${apiUrl}?action=${action}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                        body: new URLSearchParams(payload),
                    });

                    const data = await response.json();
                    if (!response.ok || data.ok === false) {
                        throw new Error(data.error || 'Une erreur est survenue.');
                    }

                    let resultHTML = '';
                    if (action === 'predict-age') {
                        resultHTML = `
                            <div class="mt-stack-md p-stack-md bg-secondary-fixed/20 border border-secondary-fixed rounded-lg">
                                <p class="font-label-sm text-label-sm text-secondary mb-unit">Résultat de prédiction</p>
                                <p class="text-h3 font-h3 text-primary">${formatNumber(data.data.age_estim, 1)} ans</p>
                                <p class="text-xs text-on-surface-variant mt-2">Âge estimé selon le modèle de régression</p>
                            </div>
                        `;
                    } else if (action === 'predict-cluster') {
                        resultHTML = `
                            <div class="mt-stack-md p-stack-md bg-tertiary-fixed/20 border border-tertiary-fixed rounded-lg">
                                <p class="font-label-sm text-label-sm text-tertiary mb-unit">Résultat de classification</p>
                                <p class="text-h3 font-h3 text-primary">Catégorie: ${data.data.cluster || 'Inconnue'}</p>
                                <p class="text-xs text-on-surface-variant mt-2">Classification de gabarit effectuée</p>
                            </div>
                        `;
                    } else if (action === 'predict-alert') {
                        const probability = data.data.probability === null ? 'N/A' : `${formatNumber(data.data.probability * 100, 1)} %`;
                        const alertClass = data.data.alert ? 'error-container' : 'secondary-fixed';
                        const alertBorder = data.data.alert ? 'error-container' : 'secondary-fixed';
                        const alertText = data.data.alert ? 'ALERTE DÉTECTÉE' : 'RISQUE FAIBLE';
                        resultHTML = `
                            <div class="mt-stack-md p-stack-md bg-${alertClass}/20 border border-${alertBorder} rounded-lg">
                                <p class="font-label-sm text-label-sm text-primary mb-unit">Analyse de risque tempête</p>
                                <p class="text-h3 font-h3 ${data.data.alert ? 'text-error' : 'text-secondary'}">${alertText}</p>
                                <p class="text-body-md text-on-surface-variant mt-2">Probabilité de risque: ${probability}</p>
                            </div>
                        `;
                    } else {
                        resultHTML = '<div class="mt-stack-md p-stack-md bg-secondary-fixed/20 border border-secondary-fixed rounded-lg"><p class="font-label-sm text-secondary">✓ Succès</p></div>';
                    }
                    
                    if (result) {
                        result.innerHTML = resultHTML;
                    }
                } catch (error) {
                    if (result) {
                        result.innerHTML = `<div class="mt-stack-md p-stack-md bg-error-container/20 border border-error rounded-lg text-error">${error.message}</div>`;
                    }
                }
            });
        });
    };

    const initMap = async () => {
        const mapContainer = document.querySelector('[data-map]');
        if (!mapContainer || typeof L === 'undefined') {
            return;
        }

        const map = L.map(mapContainer).setView([49.8489, 3.2877], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const caption = document.querySelector('[data-map-caption]');
        const title = document.querySelector('[data-map-title]');
        const countEl = document.querySelector('[data-map-count]');
        const legendEl = document.querySelector('[data-map-legend]');
        const tabs = document.querySelectorAll('[data-map-tab]');
        let layerGroup = L.layerGroup().addTo(map);

        const legends = {
            age: [
                { color: '#2f7d32', label: 'Jeune (< 40 ans)' },
                { color: '#c97d2d', label: 'Mature (40–80 ans)' },
                { color: '#8b1e3f', label: 'Ancien (> 80 ans)' },
            ],
            cluster: [
                { color: '#3caea3', label: 'Petit gabarit (< 0,4 m)' },
                { color: '#20639b', label: 'Gabarit moyen (0,4–0,8 m)' },
                { color: '#173f5f', label: 'Grand gabarit (> 0,8 m)' },
            ],
        };

        const updateLegend = (mode) => {
            if (!legendEl) return;
            const items = legends[mode] ?? [];
            legendEl.innerHTML = items.map((item, i) => `
                <div class="flex items-center gap-2 ${i < items.length - 1 ? 'mb-2 pb-2 border-b border-outline-variant' : ''}">
                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:${item.color}"></span>
                    <span class="text-label-sm text-primary font-semibold">${item.label}</span>
                </div>
            `).join('');
        };

        const loadLayer = async (mode) => {
            layerGroup.clearLayers();
            if (caption && title) {
                title.textContent = mode === 'age' ? 'Carte âge' : mode === 'cluster' ? 'Carte gabarit' : 'Carte vigilance';
                caption.textContent = mode === 'age'
                    ? "Les points sont colorés selon les classes d'âge estimé."
                    : mode === 'cluster'
                        ? "Les points sont colorés selon le gabarit des arbres."
                        : "Les points sont colorés selon la vigilance et l'intérêt patrimonial.";
            }
            updateLegend(mode);

            const data = await json(await fetch(`${apiUrl}?action=map&mode=${encodeURIComponent(mode)}`));
            if (countEl) countEl.textContent = formatNumber(data.points.length);
            data.points.forEach((point) => {
                const marker = L.circleMarker([point.lat, point.lng], {
                    radius: point.style.radius,
                    color: point.style.color,
                    fillColor: point.style.color,
                    fillOpacity: 0.8,
                    weight: 2,
                }).bindPopup(`<strong>${point.title}</strong><br>${point.description}<br>${point.style.label}`);
                marker.addTo(layerGroup);
            });

            if (data.points.length) {
                const bounds = L.latLngBounds(data.points.map((point) => [point.lat, point.lng]));
                map.fitBounds(bounds.pad(0.18));
            }
        };

        tabs.forEach((tab) => {
            tab.addEventListener('click', async () => {
                tabs.forEach((node) => {
                    node.classList.remove('is-active');
                    node.classList.add('border-transparent', 'text-on-surface-variant');
                    node.classList.remove('border-primary', 'text-primary');
                });
                tab.classList.add('is-active');
                tab.classList.remove('border-transparent', 'text-on-surface-variant');
                tab.classList.add('border-primary', 'text-primary');
                await loadLayer(tab.getAttribute('data-map-tab'));
            });
        });

        await loadLayer(document.querySelector('[data-map-tab].is-active')?.getAttribute('data-map-tab') || 'age');
    };

    const initGeoMap = () => {
        const mapContainer = document.querySelector('[data-geo-map]');
        if (!mapContainer || typeof L === 'undefined') {
            return;
        }

        const latitudeInput = document.querySelector('input[name="latitude"]');
        const longitudeInput = document.querySelector('input[name="longitude"]');

        const defaultLat = 49.8489;
        const defaultLng = 3.2877;
        const initialLat = Number.parseFloat(latitudeInput?.value || '');
        const initialLng = Number.parseFloat(longitudeInput?.value || '');
        const hasInitialPoint = Number.isFinite(initialLat) && Number.isFinite(initialLng);
        const startLat = hasInitialPoint ? initialLat : defaultLat;
        const startLng = hasInitialPoint ? initialLng : defaultLng;

        const map = L.map(mapContainer, {
            zoomControl: false,
            scrollWheelZoom: false,
        }).setView([startLat, startLng], hasInitialPoint ? 16 : 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

        const syncInputs = (lat, lng) => {
            if (latitudeInput) {
                latitudeInput.value = Number(lat).toFixed(6);
            }
            if (longitudeInput) {
                longitudeInput.value = Number(lng).toFixed(6);
            }
        };

        syncInputs(startLat, startLng);

        map.on('click', (event) => {
            const { lat, lng } = event.latlng;
            marker.setLatLng([lat, lng]);
            syncInputs(lat, lng);
        });

        marker.on('dragend', () => {
            const position = marker.getLatLng();
            syncInputs(position.lat, position.lng);
        });

        const zoomControl = L.control.zoom({ position: 'topright' });
        zoomControl.addTo(map);
        zoomControl.getContainer().querySelectorAll('a').forEach(a => {
            a.addEventListener('click', e => e.preventDefault());
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        fillSummary();
        fillTreesTable();
        bindForms();
        initMap();
        initGeoMap();
    });
})();
