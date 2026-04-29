<?php

declare(strict_types=1);

require_once __DIR__ . '/_init.php';

if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo file_get_contents(__DIR__ . '/openapi.json');
    exit;
}

$currentPage = 'swagger';
require_once __DIR__ . '/partials/header.php';
?>

<main class="flex-grow w-full px-container-padding py-stack-lg">
    <div class="max-w-[1280px] mx-auto mb-stack-lg">
        <div class="mb-stack-lg">
            <h1 class="font-h1 text-h1 text-primary mb-2">Documentation API</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant">
                Toutes les routes sont servies par <code class="bg-surface-container px-1 rounded text-sm font-mono">api.php</code> via le paramètre <code class="bg-surface-container px-1 rounded text-sm font-mono">action</code>.
                La spec OpenAPI est également disponible au format JSON :
                <a href="swagger.php?format=json" class="text-secondary underline">swagger.php?format=json</a>.
            </p>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <div id="swagger-ui" class="max-w-[1280px] mx-auto"></div>
</main>

<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
const spec = {
    openapi: '3.0.3',
    info: {
        title: 'Saint-Quentin API',
        version: '1.0.0',
        description: 'API REST du patrimoine arboré de Saint-Quentin.\n\n> **Note** : toutes les opérations transitent par `api.php`. Le paramètre `action` est fixé par opération et indiqué ci-dessous. Le "Try it out" fonctionne depuis le même serveur.',
    },
    servers: [{ url: '', description: 'Serveur local (même répertoire)' }],
    tags: [
        { name: 'Données', description: 'Consultation des arbres et statistiques' },
        { name: 'Carte', description: 'Points géolocalisés pour la carte' },
        { name: 'IA – Prédiction', description: 'Modèles de machine learning' },
        { name: 'Gestion', description: 'Ajout et modification des arbres' },
    ],
    paths: {
        '/api.php': {
            get: {
                tags: ['Données'],
                summary: 'Résumé statistique',
                description: 'Retourne les statistiques globales (total arbres, remarquables, hauteur moyenne, âge moyen, arbres retirés) ainsi que la répartition par quartier et stade de développement.\n\n**URL réelle :** `api.php?action=summary`',
                operationId: 'getSummary',
                parameters: [
                    { name: 'action', in: 'query', required: true, schema: { type: 'string', enum: ['summary'] } },
                ],
                responses: {
                    200: {
                        description: 'Succès',
                        content: {
                            'application/json': {
                                schema: {
                                    type: 'object',
                                    properties: {
                                        ok: { type: 'boolean', example: true },
                                        data: {
                                            type: 'object',
                                            properties: {
                                                summary: {
                                                    type: 'object',
                                                    properties: {
                                                        total: { type: 'integer', example: 4821 },
                                                        remarkable: { type: 'integer', example: 312 },
                                                        avg_height: { type: 'number', example: 12.4 },
                                                        avg_age: { type: 'number', example: 38.2 },
                                                        removed_or_replaced: { type: 'integer', example: 45 },
                                                    },
                                                },
                                                by_quartier: {
                                                    type: 'array',
                                                    items: {
                                                        type: 'object',
                                                        properties: {
                                                            clc_quartier: { type: 'string' },
                                                            total: { type: 'integer' },
                                                        },
                                                    },
                                                },
                                                by_stade: {
                                                    type: 'array',
                                                    items: {
                                                        type: 'object',
                                                        properties: {
                                                            fk_stadedev: { type: 'string' },
                                                            total: { type: 'integer' },
                                                        },
                                                    },
                                                },
                                            },
                                        },
                                    },
                                },
                            },
                        },
                    },
                    500: { $ref: '#/components/responses/DbError' },
                },
            },
        },
        '/api.php/trees': {
            get: {
                tags: ['Données'],
                summary: 'Liste paginée des arbres',
                description: 'Retourne une liste d\'arbres filtrée et paginée.\n\n**URL réelle :** `api.php?action=trees`',
                operationId: 'listTrees',
                parameters: [
                    { name: 'action', in: 'query', required: true, schema: { type: 'string', enum: ['trees'] } },
                    { name: 'page', in: 'query', schema: { type: 'integer', default: 1, minimum: 1 }, description: 'Numéro de page' },
                    { name: 'limit', in: 'query', schema: { type: 'integer', default: 25, minimum: 1, maximum: 100 }, description: 'Résultats par page' },
                    { name: 'quartier', in: 'query', schema: { type: 'string' }, description: 'Filtre sur le quartier (ILIKE)' },
                    { name: 'secteur', in: 'query', schema: { type: 'string' }, description: 'Filtre sur le secteur (ILIKE)' },
                    { name: 'stade', in: 'query', schema: { type: 'string' }, description: 'Filtre sur le stade de développement (ILIKE)' },
                    { name: 'remarquable', in: 'query', schema: { type: 'string', enum: ['', '1', '0'] }, description: 'Filtre arbres remarquables' },
                    { name: 'q', in: 'query', schema: { type: 'string' }, description: 'Recherche textuelle (nom, quartier, secteur)' },
                ],
                responses: {
                    200: {
                        description: 'Succès',
                        content: {
                            'application/json': {
                                schema: {
                                    type: 'object',
                                    properties: {
                                        ok: { type: 'boolean' },
                                        data: {
                                            type: 'object',
                                            properties: {
                                                items: {
                                                    type: 'array',
                                                    items: { $ref: '#/components/schemas/Tree' },
                                                },
                                                total: { type: 'integer' },
                                                page: { type: 'integer' },
                                                limit: { type: 'integer' },
                                                pages: { type: 'integer' },
                                            },
                                        },
                                    },
                                },
                            },
                        },
                    },
                    500: { $ref: '#/components/responses/DbError' },
                },
            },
        },
        '/api.php/map': {
            get: {
                tags: ['Carte'],
                summary: 'Points géolocalisés pour la carte',
                description: 'Retourne la liste des arbres géolocalisés avec leur style visuel selon le mode choisi.\n\n**URL réelle :** `api.php?action=map`\n\n| Mode | Couleurs | Description |\n|------|----------|-------------|\n| `age` | vert / orange / rouge | Jeune / Mature / Ancien |\n| `cluster` | bleu clair / bleu / bleu foncé | Petit / Moyen / Grand gabarit |',
                operationId: 'getMapPoints',
                parameters: [
                    { name: 'action', in: 'query', required: true, schema: { type: 'string', enum: ['map'] } },
                    {
                        name: 'mode',
                        in: 'query',
                        required: false,
                        schema: { type: 'string', enum: ['age', 'cluster'], default: 'age' },
                        description: 'Mode de coloration des marqueurs',
                    },
                ],
                responses: {
                    200: {
                        description: 'Succès',
                        content: {
                            'application/json': {
                                schema: {
                                    type: 'object',
                                    properties: {
                                        ok: { type: 'boolean' },
                                        data: {
                                            type: 'object',
                                            properties: {
                                                mode: { type: 'string', example: 'age' },
                                                total: { type: 'integer' },
                                                points: {
                                                    type: 'array',
                                                    items: { $ref: '#/components/schemas/MapPoint' },
                                                },
                                            },
                                        },
                                    },
                                },
                            },
                        },
                    },
                    500: { $ref: '#/components/responses/DbError' },
                },
            },
        },
        '/api.php/add-tree': {
            post: {
                tags: ['Gestion'],
                summary: 'Ajouter un arbre',
                description: 'Insère un nouvel arbre dans la base de données. Si les champs morphologiques suffisants sont présents, le modèle IA d\'alerte tempête est également exécuté automatiquement.\n\n**URL réelle :** `api.php?action=add-tree`',
                operationId: 'addTree',
                requestBody: {
                    required: true,
                    content: {
                        'application/x-www-form-urlencoded': {
                            schema: {
                                type: 'object',
                                required: ['action'],
                                properties: {
                                    action: { type: 'string', enum: ['add-tree'] },
                                    clc_quartier: { type: 'string', description: 'Quartier' },
                                    clc_secteur: { type: 'string', description: 'Secteur' },
                                    id_arbre: { type: 'integer', description: 'Identifiant arbre (optionnel)' },
                                    haut_tot: { type: 'number', description: 'Hauteur totale (m)' },
                                    haut_tronc: { type: 'number', description: 'Hauteur du tronc (m)' },
                                    tronc_diam: { type: 'number', description: 'Diamètre du tronc (m)' },
                                    fk_arb_etat: { type: 'integer', description: 'État de l\'arbre (FK)' },
                                    fk_stadedev: { type: 'string', description: 'Stade de développement' },
                                    fk_port: { type: 'string', description: 'Port de l\'arbre' },
                                    fk_pied: { type: 'string', description: 'Type de pied' },
                                    fk_situation: { type: 'string', description: 'Situation' },
                                    fk_revetement: { type: 'string', description: 'Revêtement' },
                                    age_estim: { type: 'number', description: 'Âge estimé (années)' },
                                    fk_prec_estim: { type: 'integer', description: 'Précision estimation (FK)' },
                                    clc_nbr_diag: { type: 'integer', description: 'Nombre de diagnostics' },
                                    fk_nomtech: { type: 'string', description: 'Nom technique (FK)' },
                                    villeca: { type: 'string', description: 'Commune' },
                                    nomfrancais: { type: 'string', description: 'Nom français' },
                                    nomlatin: { type: 'string', description: 'Nom latin' },
                                    feuillage: { type: 'string', description: 'Type de feuillage' },
                                    remarquable: { type: 'integer', enum: [0, 1], description: 'Arbre remarquable' },
                                    longitude: { type: 'number', description: 'Longitude WGS84' },
                                    latitude: { type: 'number', description: 'Latitude WGS84' },
                                },
                            },
                        },
                    },
                },
                responses: {
                    200: {
                        description: 'Arbre inséré',
                        content: {
                            'application/json': {
                                schema: {
                                    type: 'object',
                                    properties: {
                                        ok: { type: 'boolean' },
                                        data: {
                                            type: 'object',
                                            properties: {
                                                id: { type: 'integer', nullable: true, description: 'ID de l\'arbre inséré' },
                                                alerte_tempete: { type: 'boolean', nullable: true, description: 'Résultat alerte IA (si calculé)' },
                                            },
                                        },
                                    },
                                },
                            },
                        },
                    },
                    500: { $ref: '#/components/responses/DbError' },
                },
            },
        },
        '/api.php/predict-age': {
            post: {
                tags: ['IA – Prédiction'],
                summary: 'Prédire l\'âge d\'un arbre',
                description: 'Estime l\'âge d\'un arbre à partir de ses dimensions morphologiques via un modèle de régression.\n\n**URL réelle :** `api.php?action=predict-age`',
                operationId: 'predictAge',
                requestBody: {
                    required: true,
                    content: {
                        'application/x-www-form-urlencoded': {
                            schema: {
                                type: 'object',
                                required: ['action', 'haut_tot', 'haut_tronc', 'tronc_diam'],
                                properties: {
                                    action: { type: 'string', enum: ['predict-age'] },
                                    haut_tot: { type: 'number', description: 'Hauteur totale (m)', example: 15 },
                                    haut_tronc: { type: 'number', description: 'Hauteur du tronc (m)', example: 3.5 },
                                    tronc_diam: { type: 'number', description: 'Diamètre du tronc (m)', example: 0.6 },
                                    clc_nbr_diag: { type: 'integer', default: 0, description: 'Nombre de diagnostics' },
                                    remarquable: { type: 'string', enum: ['0', '1', 'Oui', 'Non'], default: '0' },
                                },
                            },
                        },
                    },
                },
                responses: {
                    200: {
                        description: 'Prédiction réussie',
                        content: {
                            'application/json': {
                                schema: { $ref: '#/components/schemas/PredictionResponse' },
                                example: { ok: true, data: { age_estim: 45, raw_output: '45' }, error: null },
                            },
                        },
                    },
                    400: { $ref: '#/components/responses/ValidationError' },
                    500: { $ref: '#/components/responses/PredictionError' },
                },
            },
        },
        '/api.php/predict-cluster': {
            post: {
                tags: ['IA – Prédiction'],
                summary: 'Classer le gabarit d\'un arbre',
                description: 'Détermine le groupe de gabarit (petit / moyen / grand) d\'un arbre via un modèle de clustering.\n\n**URL réelle :** `api.php?action=predict-cluster`',
                operationId: 'predictCluster',
                requestBody: {
                    required: true,
                    content: {
                        'application/x-www-form-urlencoded': {
                            schema: {
                                type: 'object',
                                required: ['action', 'haut_tot', 'haut_tronc', 'tronc_diam'],
                                properties: {
                                    action: { type: 'string', enum: ['predict-cluster'] },
                                    haut_tot: { type: 'number', description: 'Hauteur totale (m)', example: 15 },
                                    haut_tronc: { type: 'number', description: 'Hauteur du tronc (m)', example: 3.5 },
                                    tronc_diam: { type: 'number', description: 'Diamètre du tronc (m)', example: 0.6 },
                                    k: { type: 'integer', default: 2, description: 'Nombre de clusters' },
                                },
                            },
                        },
                    },
                },
                responses: {
                    200: {
                        description: 'Classification réussie',
                        content: {
                            'application/json': {
                                schema: { $ref: '#/components/schemas/PredictionResponse' },
                                example: { ok: true, data: { cluster: 1, raw_output: '1' }, error: null },
                            },
                        },
                    },
                    400: { $ref: '#/components/responses/ValidationError' },
                    500: { $ref: '#/components/responses/PredictionError' },
                },
            },
        },
        '/api.php/predict-alert': {
            post: {
                tags: ['IA – Prédiction'],
                summary: 'Évaluer le risque d\'alerte tempête',
                description: 'Prédit si un arbre présente un risque élevé lors d\'une tempête via un modèle Random Forest.\n\n**URL réelle :** `api.php?action=predict-alert`',
                operationId: 'predictAlert',
                requestBody: {
                    required: true,
                    content: {
                        'application/x-www-form-urlencoded': {
                            schema: {
                                type: 'object',
                                required: ['action', 'haut_tot', 'haut_tronc', 'tronc_diam', 'age_estim', 'fk_stadedev', 'fk_port', 'fk_pied', 'fk_situation', 'fk_revetement', 'feuillage'],
                                properties: {
                                    action: { type: 'string', enum: ['predict-alert'] },
                                    haut_tot: { type: 'number', description: 'Hauteur totale (m)', example: 18 },
                                    haut_tronc: { type: 'number', description: 'Hauteur du tronc (m)', example: 4 },
                                    tronc_diam: { type: 'number', description: 'Diamètre du tronc (m)', example: 0.9 },
                                    age_estim: { type: 'number', description: 'Âge estimé (années)', example: 60 },
                                    fk_stadedev: { type: 'string', description: 'Stade de développement', example: 'Adulte' },
                                    fk_port: { type: 'string', description: 'Port de l\'arbre', example: 'Étalé' },
                                    fk_pied: { type: 'string', description: 'Type de pied', example: 'Enherbé' },
                                    fk_situation: { type: 'string', description: 'Situation', example: 'Isolé' },
                                    fk_revetement: { type: 'string', description: 'Revêtement', example: 'Enrobé' },
                                    feuillage: { type: 'string', description: 'Type de feuillage', example: 'Caduc' },
                                },
                            },
                        },
                    },
                },
                responses: {
                    200: {
                        description: 'Prédiction réussie',
                        content: {
                            'application/json': {
                                schema: {
                                    type: 'object',
                                    properties: {
                                        ok: { type: 'boolean' },
                                        data: {
                                            type: 'object',
                                            properties: {
                                                alert: { type: 'boolean', description: 'true = risque élevé' },
                                                raw_output: { type: 'string' },
                                            },
                                        },
                                        error: { type: 'string', nullable: true },
                                    },
                                },
                                example: { ok: true, data: { alert: false, raw_output: '0' }, error: null },
                            },
                        },
                    },
                    400: { $ref: '#/components/responses/ValidationError' },
                    500: { $ref: '#/components/responses/PredictionError' },
                },
            },
        },
    },
    components: {
        schemas: {
            Tree: {
                type: 'object',
                properties: {
                    id_arbre: { type: 'integer' },
                    clc_quartier: { type: 'string' },
                    clc_secteur: { type: 'string' },
                    nomfrancais: { type: 'string' },
                    nomlatin: { type: 'string' },
                    fk_nomtech: { type: 'string' },
                    haut_tot: { type: 'number' },
                    haut_tronc: { type: 'number' },
                    tronc_diam: { type: 'number' },
                    age_estim: { type: 'number' },
                    remarquable: { type: 'integer', enum: [0, 1] },
                    latitude: { type: 'number' },
                    longitude: { type: 'number' },
                    alerte_tempete: { type: 'boolean', nullable: true },
                },
            },
            MapPoint: {
                type: 'object',
                properties: {
                    id: { type: 'integer' },
                    lat: { type: 'number' },
                    lng: { type: 'number' },
                    title: { type: 'string' },
                    description: { type: 'string' },
                    age: { type: 'number' },
                    height: { type: 'number' },
                    diameter: { type: 'number' },
                    style: {
                        type: 'object',
                        properties: {
                            color: { type: 'string', example: '#2f7d32' },
                            radius: { type: 'integer' },
                            label: { type: 'string' },
                        },
                    },
                },
            },
            PredictionResponse: {
                type: 'object',
                properties: {
                    ok: { type: 'boolean' },
                    data: { type: 'object' },
                    error: { type: 'string', nullable: true },
                },
            },
        },
        responses: {
            DbError: {
                description: 'Erreur base de données',
                content: {
                    'application/json': {
                        schema: {
                            type: 'object',
                            properties: {
                                ok: { type: 'boolean', example: false },
                                error: { type: 'string' },
                            },
                        },
                    },
                },
            },
            ValidationError: {
                description: 'Paramètre manquant ou invalide',
                content: {
                    'application/json': {
                        schema: {
                            type: 'object',
                            properties: {
                                ok: { type: 'boolean', example: false },
                                error: { type: 'string' },
                            },
                        },
                    },
                },
            },
            PredictionError: {
                description: 'Erreur du modèle IA',
                content: {
                    'application/json': {
                        schema: {
                            type: 'object',
                            properties: {
                                ok: { type: 'boolean', example: false },
                                error: { type: 'string' },
                                raw_output: { type: 'string', nullable: true },
                            },
                        },
                    },
                },
            },
        },
    },
};

SwaggerUIBundle({
    spec,
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
    layout: 'BaseLayout',
    defaultModelsExpandDepth: 1,
    defaultModelExpandDepth: 2,
    docExpansion: 'list',
    tryItOutEnabled: false,
});
</script>

<style>
    .swagger-ui .topbar { display: none; }
    .swagger-ui .info { margin: 0 0 1.5rem; }
    .swagger-ui .info .title { color: #012d1d; font-size: 1.5rem; }
    .swagger-ui .opblock-tag { color: #012d1d; border-color: #c1c8c2; }
    .swagger-ui .opblock.opblock-get .opblock-summary-method { background: #2c694e; }
    .swagger-ui .opblock.opblock-post .opblock-summary-method { background: #3f6653; }
    .swagger-ui .opblock.opblock-get { border-color: #2c694e; background: rgba(44,105,78,0.04); }
    .swagger-ui .opblock.opblock-post { border-color: #3f6653; background: rgba(63,102,83,0.04); }
    .swagger-ui .btn.execute { background: #2c694e; border-color: #2c694e; }
    .swagger-ui .btn.execute:hover { background: #012d1d; }
    .swagger-ui section.models { display: none; }
</style>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
