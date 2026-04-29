<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TreeModel;

class MapController
{
    private TreeModel $treeModel;

    public function __construct(TreeModel $treeModel)
    {
        $this->treeModel = $treeModel;
    }

    /**
     * Display maps page with tabs
     */
    public function index(): array
    {
        return [
            'view' => 'maps/index',
            'data' => [
                'currentPage' => 'cartes',
                'modes' => ['age', 'cluster', 'alert'],
                'defaultMode' => 'age',
            ],
        ];
    }

    /**
     * API endpoint: get map points for visualization
     */
    public function getPoints(array $params = []): array
    {
        try {
            $mode = $params['mode'] ?? $_GET['mode'] ?? 'age';
            $filters = $params['filters'] ?? [];

            $points = $this->treeModel->getMapPoints($mode, $filters);

            return [
                'ok' => true,
                'data' => [
                    'points' => $points,
                    'mode' => $mode,
                    'count' => count($points),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'ok' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get map metadata and statistics
     */
    public function getMetadata(): array
    {
        $summary = $this->treeModel->getSummary();

        return [
            'ok' => true,
            'data' => [
                'totalTrees' => $summary['summary']['total'],
                'remarkableTrees' => $summary['summary']['remarkable'],
                'center' => [49.8489, 3.2877],
                'zoom' => 13,
            ],
        ];
    }
}
