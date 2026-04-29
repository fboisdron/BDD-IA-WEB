<?php

declare(strict_types=1);

namespace App\Models;

require_once __DIR__ . '/../../lib/TreeRepository.php';

use TreeRepository;

class TreeModel
{
    private $repository;

    public function __construct(TreeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get summary statistics for dashboard
     */
    public function getSummary(): array
    {
        return $this->repository->summary();
    }

    /**
     * Get paginated list of trees with filters
     */
    public function getTreesList(int $page = 1, int $limit = 12, array $filters = []): array
    {
        return $this->repository->listTrees(array_merge([
            'limit' => $limit,
            'offset' => ($page - 1) * $limit,
        ], $filters));
    }

    /**
     * Get map points for visualization
     */
    public function getMapPoints(string $mode = 'age', array $filters = []): array
    {
        return $this->repository->mapPoints($mode, $filters);
    }

    /**
     * Add new tree to database
     */
    public function addTree(array $data): bool
    {
        return $this->repository->insertTree($data);
    }

    /**
     * Get total count of trees
     */
    public function getTotalCount(): int
    {
        $summary = $this->getSummary();
        return (int) $summary['summary']['total'] ?? 0;
    }

    /**
     * Get trees by quartier (district)
     */
    public function getTreesByQuartier(): array
    {
        $summary = $this->getSummary();
        return $summary['by_quartier'] ?? [];
    }

    /**
     * Get trees by development stage
     */
    public function getTreesByStade(): array
    {
        $summary = $this->getSummary();
        return $summary['by_stade'] ?? [];
    }

    /**
     * Get count of remarkable trees
     */
    public function getRemarkableCount(): int
    {
        $summary = $this->getSummary();
        return (int) $summary['summary']['remarkable'] ?? 0;
    }

    /**
     * Get average height
     */
    public function getAverageHeight(): float
    {
        $summary = $this->getSummary();
        return (float) $summary['summary']['avg_height'] ?? 0;
    }

    /**
     * Get average age
     */
    public function getAverageAge(): float
    {
        $summary = $this->getSummary();
        return (float) $summary['summary']['avg_age'] ?? 0;
    }
}
