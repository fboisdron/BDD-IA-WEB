<?php

declare(strict_types=1);

final class TreeRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function summary(): array
    {
        $sql = 'SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN remarquable = 1 THEN 1 ELSE 0 END) AS remarquable,
                AVG(haut_tot) AS avg_height,
                AVG(age_estim) AS avg_age,
                SUM(CASE WHEN fk_arb_etat = 1 THEN 1 ELSE 0 END) AS removed_or_replaced
            FROM arbres';

        $summary = $this->pdo->query($sql)->fetch() ?: [];

        $byQuartier = $this->pdo->query(
            'SELECT clc_quartier, COUNT(*) AS total FROM arbres GROUP BY clc_quartier ORDER BY total DESC LIMIT 8'
        )->fetchAll();

        $byStade = $this->pdo->query(
            'SELECT fk_stadedev, COUNT(*) AS total FROM arbres GROUP BY fk_stadedev ORDER BY total DESC'
        )->fetchAll();

        return [
            'summary' => [
                'total' => (int) ($summary['total'] ?? 0),
                'remarkable' => (int) ($summary['remarquable'] ?? 0),
                'avg_height' => round((float) ($summary['avg_height'] ?? 0), 2),
                'avg_age' => round((float) ($summary['avg_age'] ?? 0), 1),
                'removed_or_replaced' => (int) ($summary['removed_or_replaced'] ?? 0),
            ],
            'by_quartier' => $byQuartier,
            'by_stade' => $byStade,
        ];
    }

    public function listTrees(array $filters = []): array
    {
        $limit = max(1, min(100, (int) ($filters['limit'] ?? 25)));
        $page = max(1, (int) ($filters['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $clauses = ['1 = 1'];
        $params = [];

        if (!empty($filters['quartier'])) {
            $clauses[] = 'clc_quartier LIKE :quartier';
            $params['quartier'] = '%' . $filters['quartier'] . '%';
        }

        if (!empty($filters['secteur'])) {
            $clauses[] = 'clc_secteur LIKE :secteur';
            $params['secteur'] = '%' . $filters['secteur'] . '%';
        }

        if (!empty($filters['stade'])) {
            $clauses[] = 'fk_stadedev = :stade';
            $params['stade'] = $filters['stade'];
        }

        if (!empty($filters['port'])) {
            $clauses[] = 'fk_port = :port';
            $params['port'] = $filters['port'];
        }

        if (!empty($filters['pied'])) {
            $clauses[] = 'fk_pied = :pied';
            $params['pied'] = $filters['pied'];
        }

        if (!empty($filters['situation'])) {
            $clauses[] = 'fk_situation = :situation';
            $params['situation'] = $filters['situation'];
        }

        if (!empty($filters['revetement'])) {
            $clauses[] = 'fk_revetement = :revetement';
            $params['revetement'] = $filters['revetement'];
        }

        if (!empty($filters['feuillage'])) {
            $clauses[] = 'feuillage = :feuillage';
            $params['feuillage'] = $filters['feuillage'];
        }

        if (!empty($filters['remarquable'])) {
            $clauses[] = 'remarquable = :remarquable';
            $params['remarquable'] = (int) $filters['remarquable'];
        }

        if (!empty($filters['q'])) {
            $clauses[] = '(fk_nomtech LIKE :q OR nomfrancais LIKE :q OR clc_quartier LIKE :q OR clc_secteur LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        $where = implode(' AND ', $clauses);

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM arbres WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sortableColumns = ['id_arbre', 'haut_tot', 'haut_tronc', 'tronc_diam', 'age_estim'];
        $sortCol = in_array($filters['sort'] ?? '', $sortableColumns, true) ? $filters['sort'] : 'id_arbre';
        $sortOrder = strtoupper($filters['order'] ?? '') === 'ASC' ? 'ASC' : 'DESC';
        $orderBy = "({$sortCol} IS NULL), {$sortCol} {$sortOrder}";

        $sql = "SELECT
                id_arbre, clc_quartier, clc_secteur, haut_tot, haut_tronc, tronc_diam,
                fk_arb_etat, fk_stadedev, fk_situation, fk_port, fk_pied, fk_revetement, age_estim,
                fk_nomtech, villeca, nomfrancais, nomlatin, feuillage, remarquable,
                longitude, latitude
            FROM arbres
            WHERE {$where}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int) max(1, ceil($total / $limit)),
            ],
        ];
    }

    public function mapPoints(string $mode, array $filters = []): array
    {
        $mode = in_array($mode, ['age', 'cluster', 'alert'], true) ? $mode : 'age';

        $stmt = $this->pdo->query(
            'SELECT id_arbre, clc_quartier, clc_secteur, haut_tot, tronc_diam,
                    fk_arb_etat, fk_nomtech, nomfrancais, age_estim, remarquable,
                    alerte_tempete, longitude, latitude
             FROM arbres
             WHERE latitude IS NOT NULL AND longitude IS NOT NULL'
        );
        $rows = $stmt->fetchAll();
        $points = [];

        foreach ($rows as $row) {
            $age = (float) ($row['age_estim'] ?? 0);
            $height = (float) ($row['haut_tot'] ?? 0);
            $diameter = (float) ($row['tronc_diam'] ?? 0);

            $style = [
                'color' => '#2f7d32',
                'radius' => max(5, min(18, $height > 0 ? $height / 1.7 : 8)),
                'label' => 'Arbre',
            ];

            if ($mode === 'age') {
                $style['color'] = $age >= 80 ? '#8b1e3f' : ($age >= 40 ? '#c97d2d' : '#2f7d32');
                $style['label'] = $age >= 80 ? 'Ancien' : ($age >= 40 ? 'Mature' : 'Jeune');
            } elseif ($mode === 'cluster') {
                $style['color'] = $diameter >= 0.8 ? '#173f5f' : ($diameter >= 0.4 ? '#20639b' : '#3caea3');
                $style['label'] = $diameter >= 0.8 ? 'Grand gabarit' : ($diameter >= 0.4 ? 'Gabarit moyen' : 'Petit gabarit');
            } else {
                $hasAlert = $row['alerte_tempete'] !== null
                    ? (bool) $row['alerte_tempete']
                    : false;
                $style['color'] = $hasAlert ? '#b42318' : '#2563eb';
                $style['label'] = $hasAlert ? 'Alerte tempête' : 'Risque faible';
            }

            $points[] = [
                'id' => $row['id_arbre'],
                'lat' => (float) $row['latitude'],
                'lng' => (float) $row['longitude'],
                'title' => trim(($row['fk_nomtech'] ?? 'Arbre') . ' - ' . ($row['clc_quartier'] ?? '')),
                'description' => trim(($row['nomfrancais'] ?? '') . ' | ' . ($row['clc_secteur'] ?? '')),
                'age' => $age,
                'height' => $height,
                'diameter' => $diameter,
                'style' => $style,
            ];
        }

        return [
            'mode' => $mode,
            'points' => $points,
            'total' => count($points),
        ];
    }

    public function insertTree(array $data): array
    {
        $normalized = [];
        foreach (TREE_INSERT_COLUMNS as $column) {
            $normalized[$column] = $data[$column] ?? null;
        }

        $normalized['remarquable'] = isset($normalized['remarquable']) ? (int) $normalized['remarquable'] : 0;

        $columns = array_keys($normalized);
        $placeholders = array_map(static function ($column) { return ':' . $column; }, $columns);

        $sql = sprintf(
            'INSERT INTO arbres (%s) VALUES (%s)',
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->pdo->prepare($sql);
        foreach ($normalized as $column => $value) {
            if ($column === 'remarquable' || $column === 'id_arbre' || $column === 'clc_nbr_diag' || $column === 'fk_prec_estim' || $column === 'fk_arb_etat') {
                $stmt->bindValue(':' . $column, $value === null ? null : (int) $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                continue;
            }

            if (in_array($column, ['haut_tot', 'haut_tronc', 'tronc_diam', 'age_estim', 'longitude', 'latitude'], true)) {
                $stmt->bindValue(':' . $column, $value === null ? null : (float) $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                continue;
            }

            $stmt->bindValue(':' . $column, $value === null || $value === '' ? null : (string) $value, $value === null || $value === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
        }

        $stmt->execute();

        return [
            'inserted' => true,
            'id' => (int) $this->pdo->lastInsertId(),
            'id_arbre' => $normalized['id_arbre'] ?? null,
        ];
    }

    public function setAlerte($id, $alerte)
    {
        $stmt = $this->pdo->prepare('UPDATE arbres SET alerte_tempete = :alerte WHERE id = :id');
        $stmt->bindValue(':alerte', $alerte, $alerte === null ? PDO::PARAM_NULL : PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
