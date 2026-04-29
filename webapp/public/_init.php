<?php

declare(strict_types=1);

// Support both local (public/ subfolder) and flat server deployment
$_appRoot = is_file(dirname(__DIR__) . '/config/app.php') ? dirname(__DIR__) : __DIR__;
require_once $_appRoot . '/config/app.php';
require_once $_appRoot . '/lib/helpers.php';
require_once $_appRoot . '/lib/Database.php';
require_once $_appRoot . '/lib/TreeRepository.php';
require_once $_appRoot . '/lib/AuthRepository.php';
require_once $_appRoot . '/lib/PythonBridge.php';

// Session setup
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/', '', false, true);
    session_start();
}

$database       = new Database();
$repository     = null;
$authRepository = null;
$dbError        = null;

try {
    $pdo            = $database->pdo();
    $repository     = new TreeRepository($pdo);
    $authRepository = new AuthRepository($pdo);
} catch (Throwable $exception) {
    $repository     = null;
    $authRepository = null;
    $dbError        = $exception->getMessage();
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
