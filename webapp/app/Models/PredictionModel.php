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
            $scriptPath = PYTHON_BIN . ' ' . escapeshellarg(APP_ROOT . '/../IA/2-modele-prediction-age/script.py');
            $arguments = [
                (float)$data['haut_tot'],
                (float)$data['haut_tronc'],
                (float)$data['tronc_diam'],
                (int)($data['clc_nbr_diag'] ?? 0),
                $data['remarquable'] ?? '0',
            ];

            $result = $this->bridge->run($scriptPath, $arguments);
            $age = $this->bridge->parseAge($result);

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
            $scriptPath = PYTHON_BIN . ' ' . escapeshellarg(APP_ROOT . '/../IA/1\ -\ Visualisation-carte/predict_cluster.py');
            $arguments = [
                (float)$data['haut_tot'],
                (float)$data['haut_tronc'],
                (float)$data['tronc_diam'],
                $k,
            ];

            $result = $this->bridge->run($scriptPath, $arguments);
            $cluster = $this->bridge->parseCluster($result);

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
            $scriptPath = PYTHON_BIN . ' ' . escapeshellarg(APP_ROOT . '/../IA/3-Systeme-alerte-tempête/predire_alerte.py');
            $arguments = [
                (float)$data['haut_tot'],
                (float)$data['haut_tronc'],
                (float)$data['tronc_diam'],
                (float)$data['age_estim'],
                $data['fk_stadedev'] ?? '',
                $data['fk_port'] ?? '',
                $data['fk_pied'] ?? '',
                $data['fk_situation'] ?? '',
                $data['fk_revetement'] ?? '',
                $data['feuillage'] ?? '',
            ];

            $result = $this->bridge->run($scriptPath, $arguments);
            $alert = $this->bridge->parseAlert($result);

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
