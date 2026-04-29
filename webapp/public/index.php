<?php

declare(strict_types=1);

// Support both local (public/ subfolder) and flat server deployment
$appPath = is_file(__DIR__ . '/../app/Application.php')
    ? __DIR__ . '/../app/Application.php'
    : __DIR__ . '/app/Application.php';
require_once $appPath;

$app = App\Application::getInstance();
$app->run();
