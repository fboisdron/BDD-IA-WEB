<?php

declare(strict_types=1);

require_once __DIR__ . '/_init.php';

if (!$repository) {
    json_response(['ok' => false, 'error' => 'Connexion PostgreSQL indisponible.', 'raw_output' => null], 500);
}

$action = request_value('action', 'summary');

$lastRawOutput = null;

try {
    if ($action === 'summary') {
        json_response(['ok' => true, 'data' => $repository->summary()]);
    }

    if ($action === 'trees') {
        json_response([
            'ok' => true,
            'data' => $repository->listTrees([
                'page'       => request_value('page', 1),
                'limit'      => request_value('limit', 25),
                'quartier'   => request_value('quartier', ''),
                'secteur'    => request_value('secteur', ''),
                'stade'      => request_value('stade', ''),
                'port'       => request_value('port', ''),
                'pied'       => request_value('pied', ''),
                'situation'  => request_value('situation', ''),
                'revetement' => request_value('revetement', ''),
                'feuillage'  => request_value('feuillage', ''),
                'remarquable' => request_value('remarquable', ''),
                'q'          => request_value('q', ''),
                'sort'       => request_value('sort', ''),
                'order'      => request_value('order', ''),
            ]),
        ]);
    }

    if ($action === 'map') {
        json_response([
            'ok' => true,
            'data' => $repository->mapPoints((string) request_value('mode', 'age')),
        ]);
    }

    if ($action === 'add-tree') {
        $payload = [];
        foreach (TREE_INSERT_COLUMNS as $column) {
            $payload[$column] = request_value($column, null);
        }

        $payload['id_arbre'] = to_nullable_int($payload['id_arbre']);
        $payload['haut_tot'] = to_nullable_float($payload['haut_tot']);
        $payload['haut_tronc'] = to_nullable_float($payload['haut_tronc']);
        $payload['tronc_diam'] = to_nullable_float($payload['tronc_diam']);
        $payload['age_estim'] = to_nullable_float($payload['age_estim']);
        $payload['fk_prec_estim'] = to_nullable_int($payload['fk_prec_estim']);
        $payload['clc_nbr_diag'] = to_nullable_int($payload['clc_nbr_diag']);
        $payload['fk_arb_etat'] = to_nullable_int($payload['fk_arb_etat']);
        $payload['longitude'] = to_nullable_float($payload['longitude']);
        $payload['latitude'] = to_nullable_float($payload['latitude']);
        $payload['remarquable'] = to_nullable_int($payload['remarquable']) ?? 0;

        $result = $repository->insertTree($payload);
        json_response(['ok' => true, 'data' => $result]);
    }

    if ($action === 'predict-age') {
        // Validate inputs
        $haut_tot = to_nullable_float(request_value('haut_tot'));
        $haut_tronc = to_nullable_float(request_value('haut_tronc'));
        $tronc_diam = to_nullable_float(request_value('tronc_diam'));

        if ($haut_tot === null || $haut_tronc === null || $tronc_diam === null) {
            json_response(['ok' => false, 'error' => 'Paramètres invalides ou manquants : haut_tot, haut_tronc, tronc_diam'], 400);
        }

        $bridge = new PythonBridge();
        $rawRemarquable = request_value('remarquable', '0');
        $remarquable = in_array(strtolower((string)$rawRemarquable), ['1', 'oui', 'o', 'true', 'yes'], true) ? 'Oui' : 'Non';

        $result = $bridge->run(APP_ROOT . '/../IA/2-modele-prediction-age/script.py', [
            'haut_tot' => $haut_tot,
            'haut_tronc' => $haut_tronc,
            'tronc_diam' => $tronc_diam,
            'clc_nbr_diag' => request_value('clc_nbr_diag', 0),
            'remarquable' => $remarquable,
        ]);

        $lastRawOutput = $result['output'] ?? null;

        json_response([
            'ok' => $result['ok'],
            'data' => [
                'age_estim' => $bridge->parseAge($result['output'] ?? ''),
                'raw_output' => $result['output'] ?? '',
            ],
            'error' => $result['ok'] ? null : 'La prédiction d’âge a échoué.',
        ], $result['ok'] ? 200 : 500);
    }

    if ($action === 'predict-cluster') {
        // Validate numeric inputs
        $haut_tot = to_nullable_float(request_value('haut_tot'));
        $haut_tronc = to_nullable_float(request_value('haut_tronc'));
        $tronc_diam = to_nullable_float(request_value('tronc_diam'));

        if ($haut_tot === null || $haut_tronc === null || $tronc_diam === null) {
            json_response(['ok' => false, 'error' => 'Paramètres invalides ou manquants : haut_tot, haut_tronc, tronc_diam'], 400);
        }

        $bridge = new PythonBridge();
        $result = $bridge->run(APP_ROOT . '/../IA/1 - Visualisation-carte/predict_cluster.py', [
            'haut_tot' => $haut_tot,
            'haut_tronc' => $haut_tronc,
            'tronc_diam' => $tronc_diam,
            'k' => (int) request_value('k', 2),
        ]);

        $lastRawOutput = $result['output'] ?? null;

        json_response([
            'ok' => $result['ok'],
            'data' => [
                'cluster' => (new PythonBridge())->parseCluster($result['output'] ?? ''),
                'raw_output' => $result['output'] ?? '',
            ],
            'error' => $result['ok'] ? null : 'La classification de gabarit a échoué.',
        ], $result['ok'] ? 200 : 500);
    }

    if ($action === 'predict-alert') {
        // Validate required numeric and string inputs
        $haut_tot = to_nullable_float(request_value('haut_tot'));
        $haut_tronc = to_nullable_float(request_value('haut_tronc'));
        $tronc_diam = to_nullable_float(request_value('tronc_diam'));
        $age_estim = to_nullable_float(request_value('age_estim'));

        $requiredStr = ['fk_stadedev','fk_port','fk_pied','fk_situation','fk_revetement','feuillage'];
        foreach ($requiredStr as $s) {
            if (trim((string) request_value($s)) === '') {
                json_response(['ok' => false, 'error' => "Paramètre manquant: $s"], 400);
            }
        }

        if ($haut_tot === null || $haut_tronc === null || $tronc_diam === null || $age_estim === null) {
            json_response(['ok' => false, 'error' => 'Paramètres numériques invalides ou manquants pour l\'alerte'], 400);
        }

        $bridge = new PythonBridge();
        $result = $bridge->run(APP_ROOT . '/../IA/3-Systeme-alerte-tempête/predire_alerte.py', [
            'haut_tot' => $haut_tot,
            'haut_tronc' => $haut_tronc,
            'tronc_diam' => $tronc_diam,
            'age_estim' => $age_estim,
            'fk_stadedev' => request_value('fk_stadedev'),
            'fk_port' => request_value('fk_port'),
            'fk_pied' => request_value('fk_pied'),
            'fk_situation' => request_value('fk_situation'),
            'fk_revetement' => request_value('fk_revetement'),
            'feuillage' => request_value('feuillage'),
        ]);

        $lastRawOutput = $result['output'] ?? null;

        $parsed = $bridge->parseAlert($result['output'] ?? '');
        json_response([
            'ok' => $result['ok'],
            'data' => $parsed + ['raw_output' => $result['output'] ?? ''],
            'error' => $result['ok'] ? null : 'Le calcul de vigilance a échoué.',
        ], $result['ok'] ? 200 : 500);
    }

    json_response(['ok' => false, 'error' => 'Action inconnue.'], 404);
} catch (Throwable $exception) {
    json_response(['ok' => false, 'error' => $exception->getMessage(), 'raw_output' => $lastRawOutput ?? null], 500);
}
