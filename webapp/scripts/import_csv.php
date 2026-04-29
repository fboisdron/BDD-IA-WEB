<?php

declare(strict_types=1);

$_appRoot = is_file(dirname(__DIR__) . '/config/app.php') ? dirname(__DIR__) : __DIR__;
require_once $_appRoot . '/config/app.php';
require_once $_appRoot . '/lib/helpers.php';
require_once $_appRoot . '/lib/Database.php';

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Ce script doit être lancé en ligne de commande.\n");
    exit(1);
}

$csvPath = $argv[1] ?? CSV_CLEAN_FILE;
if (!is_file($csvPath)) {
    fwrite(STDERR, "CSV introuvable: {$csvPath}\n");
    exit(1);
}

$database = new Database();
$pdo = $database->pdo();


$handle = fopen($csvPath, 'r');
if (!$handle) {
    fwrite(STDERR, "Impossible d'ouvrir le CSV.\n");
    exit(1);
}

$header = fgetcsv($handle);
if ($header === false) {
    fwrite(STDERR, "CSV vide.\n");
    exit(1);
}

$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
$columns = array_map(static function ($column) { return trim($column); }, $header);
$headerLookup = [];
foreach ($columns as $columnName) {
    $headerLookup[strtolower($columnName)] = $columnName;
}

$insertColumns = [];
foreach (TREE_INSERT_COLUMNS as $column) {
    if (isset($headerLookup[$column])) {
        $insertColumns[] = $column;
    }
}

$placeholders = array_map(static function ($column) { return ':' . $column; }, $insertColumns);
$sql = sprintf('INSERT INTO arbres (%s) VALUES (%s)', implode(', ', $insertColumns), implode(', ', $placeholders));
$stmt = $pdo->prepare($sql);

$count = 0;
while (($row = fgetcsv($handle)) !== false) {
    $record = array_combine($columns, $row);
    if ($record === false) {
        continue;
    }

    foreach ($insertColumns as $column) {
        $sourceColumn = $headerLookup[$column] ?? $column;
        $value = $record[$sourceColumn] ?? null;
        if ($value === '' || $value === 'N/A') {
            $value = null;
        }

        if ($column === 'remarquable') {
            $intVal = ($value === 'Oui' || $value === '1') ? 1 : 0;
            $stmt->bindValue(':' . $column, $intVal, PDO::PARAM_INT);
        } elseif (in_array($column, ['id_arbre', 'fk_arb_etat', 'fk_prec_estim', 'clc_nbr_diag'], true)) {
            $stmt->bindValue(':' . $column, $value === null ? null : (int) $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        } elseif (in_array($column, ['haut_tot', 'haut_tronc', 'tronc_diam', 'age_estim', 'longitude', 'latitude'], true)) {
            $stmt->bindValue(':' . $column, $value === null ? null : (float) $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        } else {
            $stmt->bindValue(':' . $column, $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        }
    }

    $stmt->execute();
    $count++;
}

fclose($handle);
echo "Import terminé: {$count} lignes.\n";
