<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Application.php';

$app = App\Application::getInstance();
$app->run();
