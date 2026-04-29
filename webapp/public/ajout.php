<?php

declare(strict_types=1);

require_once __DIR__ . '/_init.php';
$currentPage = 'ajout';
require_once __DIR__ . '/partials/header.php';
?>

<main class="flex-grow w-full max-w-[1280px] mx-auto px-container-padding py-stack-lg">
    <div class="mb-stack-lg">
        <h1 class="font-h1 text-h1 text-primary mb-2">Ajouter un nouvel arbre</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Enregistrez un nouvel arbre patrimonial dans le registre municipal pour la conservation de la foresterie urbaine.</p>
    </div>

    <form class="grid grid-cols-1 lg:grid-cols-12 gap-stack-lg" data-api-form="add-tree">

        <!-- Left Column: Identification & Morphology -->
        <div class="lg:col-span-7 space-y-stack-lg">

            <!-- Section: Identification -->
            <section class="bg-surface-container-lowest p-stack-lg rounded-xl signature-shadow border border-secondary-fixed">
                <div class="flex items-center gap-stack-sm mb-stack-md">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">label</span>
                    <h2 class="font-h2 text-h3 text-primary">Identification</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Quartier</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all text-on-surface" name="clc_quartier" required placeholder="Exemple: Quartier Nord">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Secteur</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all text-on-surface" name="clc_secteur" required placeholder="Exemple: Secteur Est">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">ID Arbre</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all text-on-surface" name="id_arbre" type="number" placeholder="12345">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Feuillage</label>
                        <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all text-on-surface" name="feuillage">
                            <option value="Conifère">Conifère</option>
                            <option value="Feuillu">Feuillu</option>
                            <option value="N/A">N/A</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Nom français</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all text-on-surface" name="nomfrancais" placeholder="Chêne pédonculé">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Espèce (Nom Latin)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all text-on-surface" name="nomlatin" placeholder="Quercus robur">
                    </div>
                </div>
            </section>

            <!-- Section: Morphology -->
            <section class="bg-surface-container-lowest p-stack-lg rounded-xl signature-shadow border border-secondary-fixed">
                <div class="flex items-center gap-stack-sm mb-stack-md">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">straighten</span>
                    <h2 class="font-h2 text-h3 text-primary">Morphologie</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-stack-md">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Hauteur totale (m)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="haut_tot" type="number" step="0.01" placeholder="0.0">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Hauteur tronc (m)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="haut_tronc" type="number" step="0.01" placeholder="0.0">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Diamètre tronc (m)</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="tronc_diam" type="number" step="0.01" placeholder="1.20">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Âge estimé</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="age_estim" type="number" step="0.1" placeholder="50">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md mt-stack-md">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Port</label>
                        <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_port">
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
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Stade de développement</label>
                        <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_stadedev">
                            <option value="adulte">Adulte</option>
                            <option value="jeune">Jeune</option>
                            <option value="senescent">Sénescent</option>
                            <option value="vieux">Vieux</option>
                            <option value="N/A">N/A</option>
                        </select>
                    </div>
                </div>
                <div class="mt-stack-md pt-stack-md border-t border-outline-variant">
                    <div class="flex items-center justify-between p-stack-md bg-secondary-fixed/20 rounded-lg border border-secondary-fixed">
                        <div>
                            <p class="font-label-sm text-label-sm text-primary">Arbre remarquable</p>
                            <p class="text-xs text-on-surface-variant">Valeur historique ou écologique importante</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input class="sr-only peer" name="remarquable" type="checkbox" value="1">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                        </label>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right Column: Geolocation + Additional Info -->
        <div class="lg:col-span-5 space-y-stack-lg">

            <!-- Section: Geolocation -->
            <section class="bg-surface-container-lowest p-stack-lg rounded-xl signature-shadow border border-secondary-fixed">
                <div class="flex items-center gap-stack-sm mb-stack-md">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">location_on</span>
                    <h2 class="font-h2 text-h3 text-primary">Géolocalisation</h2>
                </div>
                <div class="rounded-lg overflow-hidden border border-outline-variant mb-stack-md relative bg-surface-container-high isolate" style="height: 300px;">
                    <div class="w-full h-full" data-geo-map></div>
                </div>
                <div class="grid grid-cols-2 gap-stack-md mb-stack-md">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Latitude</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all font-mono text-sm" name="latitude" type="text" placeholder="49.8471" value="">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Longitude</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all font-mono text-sm" name="longitude" type="text" placeholder="3.2874" value="">
                    </div>
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Ville</label>
                    <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="villeca" placeholder="Saint-Quentin" value="Saint-Quentin">
                </div>
            </section>

            <!-- Section: Additional Information -->
            <section class="bg-surface-container-lowest p-stack-lg rounded-xl signature-shadow border border-secondary-fixed">
                <div class="flex items-center gap-stack-sm mb-stack-md">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">info</span>
                    <h2 class="font-h2 text-h3 text-primary">Informations supplémentaires</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Pied</label>
                        <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_pied">
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
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Situation</label>
                        <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_situation">
                            <option value="Alignement">Alignement</option>
                            <option value="Groupe">Groupe</option>
                            <option value="Isolé">Isolé</option>
                            <option value="N/A">N/A</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Revêtement</label>
                        <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="fk_revetement">
                            <option value="Non">Non</option>
                            <option value="Oui">Oui</option>
                            <option value="N/A">N/A</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-unit">Nb diagnostics</label>
                        <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 inner-focus transition-all" name="clc_nbr_diag" type="number" step="1" value="0" placeholder="0">
                    </div>
                    <input type="hidden" name="fk_prec_estim" value="0">
                    <input type="hidden" name="fk_nomtech">
                </div>
            </section>
        </div>

        <!-- Submit -->
        <div class="lg:col-span-12">
            <button class="w-full bg-primary text-white py-4 rounded-xl font-h3 flex items-center justify-center gap-2 signature-shadow hover:bg-secondary transition-all active:scale-95" type="submit">
                <span class="material-symbols-outlined">save</span>
                Enregistrer l'arbre
            </button>
        </div>

        <!-- Form Result -->
        <div class="lg:col-span-12" data-form-result></div>
    </form>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
