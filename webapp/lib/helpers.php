<?php

declare(strict_types=1);

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_value(string $key, mixed $default = null): mixed
{
    if (isset($_POST[$key])) {
        return trim((string) $_POST[$key]);
    }

    if (isset($_GET[$key])) {
        return trim((string) $_GET[$key]);
    }

    return $default;
}

function to_nullable_float(mixed $value): ?float
{
    if ($value === null || $value === '') {
        return null;
    }

    return is_numeric($value) ? (float) $value : null;
}

function to_nullable_int(mixed $value): ?int
{
    if ($value === null || $value === '') {
        return null;
    }

    return is_numeric($value) ? (int) $value : null;
}

function normalize_string(mixed $value, string $fallback = ''): string
{
    $value = trim((string) ($value ?? ''));
    return $value !== '' ? $value : $fallback;
}
