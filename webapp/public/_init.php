<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/lib/helpers.php';
require_once dirname(__DIR__) . '/lib/Database.php';
require_once dirname(__DIR__) . '/lib/TreeRepository.php';
require_once dirname(__DIR__) . '/lib/PythonBridge.php';

$database = new Database();
$repository = null;

try {
    $repository = new TreeRepository($database->pdo());
} catch (Throwable $exception) {
    $repository = null;
}
