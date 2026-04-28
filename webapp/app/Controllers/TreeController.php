<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TreeModel;

class TreeController
{
    private TreeModel $treeModel;

    public function __construct(TreeModel $treeModel)
    {
        $this->treeModel = $treeModel;
    }

    /**
     * Display add tree form page
     */
    public function create(): array
    {
        return [
            'view' => 'trees/create',
            'data' => [],
        ];
    }

    /**
     * Store new tree in database (API)
     */
    public function store(array $data): array
    {
        try {
            // Validate required fields
            $required = ['clc_quartier', 'clc_secteur', 'longitude', 'latitude'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return [
                        'ok' => false,
                        'error' => "Champ requis manquant: {$field}",
                    ];
                }
            }

            // Insert tree
            $success = $this->treeModel->addTree($data);

            if ($success) {
                return [
                    'ok' => true,
                    'data' => ['message' => 'Arbre enregistré avec succès'],
                ];
            } else {
                return [
                    'ok' => false,
                    'error' => 'Erreur lors de l\'enregistrement de l\'arbre',
                ];
            }
        } catch (\Exception $e) {
            return [
                'ok' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Display trees catalog
     */
    public function index(): array
    {
        $page = (int)($_GET['page'] ?? 1);
        $trees = $this->treeModel->getTreesList($page, 12);

        return [
            'view' => 'trees/index',
            'data' => [
                'trees' => $trees['items'],
                'pagination' => $trees['pagination'] ?? [],
                'page' => $page,
            ],
        ];
    }

    /**
     * API endpoint: list trees with pagination
     */
    public function list(array $params = []): array
    {
        $page = (int)($params['page'] ?? $_GET['page'] ?? 1);
        $limit = (int)($params['limit'] ?? $_GET['limit'] ?? 12);

        $trees = $this->treeModel->getTreesList($page, $limit);

        return [
            'ok' => true,
            'data' => $trees,
        ];
    }

    /**
     * API endpoint: get statistics summary
     */
    public function stats(): array
    {
        $summary = $this->treeModel->getSummary();

        return [
            'ok' => true,
            'data' => [
                'total' => $summary['summary']['total'],
                'remarkable' => $summary['summary']['remarkable'],
                'avgHeight' => $summary['summary']['avg_height'],
                'avgAge' => $summary['summary']['avg_age'],
                'byQuartier' => $summary['by_quartier'],
                'byStade' => $summary['by_stade'],
            ],
        ];
    }
}
