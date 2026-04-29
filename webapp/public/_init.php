<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/lib/helpers.php';
require_once dirname(__DIR__) . '/lib/Database.php';
require_once dirname(__DIR__) . '/lib/TreeRepository.php';
require_once dirname(__DIR__) . '/lib/AuthRepository.php';
require_once dirname(__DIR__) . '/lib/PythonBridge.php';

// Session setup
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

$database       = new Database();
$repository     = null;
$authRepository = null;

try {
    $pdo            = $database->pdo();
    $repository     = new TreeRepository($pdo);
    $authRepository = new AuthRepository($pdo);
} catch (Throwable $exception) {
    $repository     = null;
    $authRepository = null;
}

// Auth guard — exempt: login.php, logout.php
$currentScript = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
$publicPages   = ['login.php', 'logout.php'];

if (!in_array($currentScript, $publicPages, true) && !isset($_SESSION['user'])) {
    if ($currentScript === 'api.php') {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'Non authentifié.']);
        exit;
    }
    $redirect = $_SERVER['REQUEST_URI'] ?? '';
    header('Location: login.php' . ($redirect ? '?redirect=' . urlencode($redirect) : ''));
    exit;
}
