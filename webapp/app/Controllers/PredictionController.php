<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\PredictionModel;

class PredictionController
{
    private PredictionModel $predictionModel;

    public function __construct(PredictionModel $predictionModel)
    {
        $this->predictionModel = $predictionModel;
    }

    /**
     * Display predictions page
     */
    public function index(): array
    {
        return [
            'view' => 'predictions/index',
            'data' => ['currentPage' => 'predictions'],
        ];
    }

    /**
     * API endpoint: predict tree age
     */
    public function predictAge(array $data = []): array
    {
        $input = $data ?: $_POST;

        $result = $this->predictionModel->predictAge($input);

        return [
            'ok' => $result['success'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
        ];
    }

    /**
     * API endpoint: predict tree cluster
     */
    public function predictCluster(array $data = []): array
    {
        $input = $data ?: $_POST;

        $result = $this->predictionModel->predictCluster($input);

        return [
            'ok' => $result['success'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
        ];
    }

    /**
     * API endpoint: predict storm alert
     */
    public function predictAlert(array $data = []): array
    {
        $input = $data ?: $_POST;

        $result = $this->predictionModel->predictAlert($input);

        return [
            'ok' => $result['success'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
        ];
    }
}
