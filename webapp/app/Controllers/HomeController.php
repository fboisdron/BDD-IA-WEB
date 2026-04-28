<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TreeModel;

class HomeController
{
    private TreeModel $treeModel;

    public function __construct(TreeModel $treeModel)
    {
        $this->treeModel = $treeModel;
    }

    /**
     * Display homepage with dashboard
     */
    public function index(): array
    {
        $summary = $this->treeModel->getSummary();
        $recentTrees = $this->treeModel->getTreesList(1, 12);

        return [
            'view' => 'home/index',
            'data' => [
                'summary' => $summary['summary'],
                'byQuartier' => $summary['by_quartier'],
                'byStade' => $summary['by_stade'],
                'recentTrees' => $recentTrees['items'],
                'pagination' => $recentTrees['pagination'] ?? [],
            ],
        ];
    }

    /**
     * API endpoint for summary data
     */
    public function getSummaryData(): array
    {
        return $this->treeModel->getSummary();
    }
}
