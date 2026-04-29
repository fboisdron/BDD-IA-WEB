<?php

declare(strict_types=1);

namespace App\Models;

require_once __DIR__ . '/../../lib/PythonBridge.php';

use PythonBridge;

class PredictionModel
{
    private PythonBridge $bridge;

    public function __construct(PythonBridge $bridge)
    {
        $this->bridge = $bridge;
    }

    /**
     * Predict tree age based on morphological features
     */
    public function predictAge(array $data): array
    {
        try {
            // Validate required numeric inputs
            $hautTot = $data['haut_tot'] ?? null;
            $hautTronc = $data['haut_tronc'] ?? null;
            $troncDiam = $data['tronc_diam'] ?? null;

            if ($hautTot === null || $hautTronc === null || $troncDiam === null || !is_numeric((string)$hautTot) || !is_numeric((string)$hautTronc) || !is_numeric((string)$troncDiam)) {
                return [
                    'success' => false,
                    'error' => 'Paramètres invalides ou manquants pour la prédiction d\'âge (haut_tot, haut_tronc, tronc_diam).',
                ];
            }

            $scriptPath = APP_ROOT . '/../IA/2-modele-prediction-age/script.py';
            $rawRemarquable = $data['remarquable'] ?? '0';
            $remarquable = in_array(strtolower((string)$rawRemarquable), ['1', 'oui', 'o', 'true', 'yes'], true) ? 'Oui' : 'Non';
            $arguments = [
                'haut_tot' => (float)$data['haut_tot'],
                'haut_tronc' => (float)$data['haut_tronc'],
                'tronc_diam' => (float)$data['tronc_diam'],
                'clc_nbr_diag' => (int)($data['clc_nbr_diag'] ?? 0),
                'remarquable' => $remarquable,
            ];

            $result = $this->bridge->run($scriptPath, $arguments);
            $age = $this->bridge->parseAge($result['output'] ?? '');

            if (!$result['ok'] || $age === null) {
                return [
                    'success' => false,
                    'error' => $result['output'] ?: 'La prédiction d’âge a échoué.',
                ];
            }

            return [
                'success' => true,
                'age_estim' => $age,
                'data' => ['age_estim' => $age],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Predict tree clustering category
     */
    public function predictCluster(array $data): array
    {
        try {
            $k = (int)($data['k'] ?? 2);
            // Validate numeric inputs
            if (!isset($data['haut_tot']) || !isset($data['haut_tronc']) || !isset($data['tronc_diam'])
                || !is_numeric((string)$data['haut_tot']) || !is_numeric((string)$data['haut_tronc']) || !is_numeric((string)$data['tronc_diam'])) {
                return [
                    'success' => false,
                    'error' => 'Paramètres invalides ou manquants pour la classification (haut_tot, haut_tronc, tronc_diam).',
                ];
            }
            $scriptPath = APP_ROOT . '/../IA/1 - Visualisation-carte/predict_cluster.py';
            $arguments = [
                'haut_tot' => (float)$data['haut_tot'],
                'haut_tronc' => (float)$data['haut_tronc'],
                'tronc_diam' => (float)$data['tronc_diam'],
                'k' => $k,
            ];

            $result = $this->bridge->run($scriptPath, $arguments);
            $cluster = $this->bridge->parseCluster($result['output'] ?? '');

            if (!$result['ok'] || $cluster === null) {
                return [
                    'success' => false,
                    'error' => $result['output'] ?: 'La classification de gabarit a échoué.',
                ];
            }

            return [
                'success' => true,
                'cluster' => $cluster,
                'data' => ['cluster' => $cluster],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Predict storm alert risk
     */
    public function predictAlert(array $data): array
    {
        try {
            // Validate required inputs
            $requiredNums = ['haut_tot','haut_tronc','tronc_diam','age_estim'];
            foreach ($requiredNums as $n) {
                if (!isset($data[$n]) || !is_numeric((string)$data[$n])) {
                    return [
                        'success' => false,
                        'error' => "Paramètre numérique manquant ou invalide: $n",
                    ];
                }
            }

            $requiredStr = ['fk_stadedev','fk_port','fk_pied','fk_situation','fk_revetement','feuillage'];
            foreach ($requiredStr as $s) {
                if (!isset($data[$s]) || trim((string)$data[$s]) === '') {
                    return [
                        'success' => false,
                        'error' => "Paramètre manquant: $s",
                    ];
                }
            }
            $scriptPath = APP_ROOT . '/../IA/3-Systeme-alerte-tempête/predire_alerte.py';
            $arguments = [
                'haut_tot' => (float)$data['haut_tot'],
                'haut_tronc' => (float)$data['haut_tronc'],
                'tronc_diam' => (float)$data['tronc_diam'],
                'age_estim' => (float)$data['age_estim'],
                'fk_stadedev' => $data['fk_stadedev'] ?? '',
                'fk_port' => $data['fk_port'] ?? '',
                'fk_pied' => $data['fk_pied'] ?? '',
                'fk_situation' => $data['fk_situation'] ?? '',
                'fk_revetement' => $data['fk_revetement'] ?? '',
                'feuillage' => $data['feuillage'] ?? '',
            ];

            $result = $this->bridge->run($scriptPath, $arguments);
            $alert = $this->bridge->parseAlert($result['output'] ?? '');

            if (!$result['ok']) {
                return [
                    'success' => false,
                    'error' => $result['output'] ?: 'Le calcul de vigilance a échoué.',
                ];
            }

            return [
                'success' => true,
                'alert' => $alert['alert'],
                'probability' => $alert['probability'],
                'data' => $alert,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
