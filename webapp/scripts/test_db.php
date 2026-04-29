#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Test script to verify database connectivity
 * Usage: php test_db.php [mysql|pgsql]
 */

require_once dirname(__DIR__) . '/webapp/config/app.php';
require_once dirname(__DIR__) . '/webapp/lib/Database.php';

// Allow override of DB type via command line
if (isset($argv[1])) {
    if (!defined('DB_TYPE')) {
        define('DB_TYPE_OVERRIDE', $argv[1]);
    }
}

$dbTypeLabel = DB_TYPE === 'mysql' ? 'MySQL' : 'PostgreSQL';

echo "\n=== Test de connexion à la base de données ===\n";
echo "Type: $dbTypeLabel\n";
echo "Host: " . DB_HOST . "\n";
echo "Port: " . DB_PORT . "\n";
echo "Database: " . DB_NAME . "\n";
echo "User: " . DB_USER . "\n\n";

try {
    $database = new Database();
    $pdo = $database->pdo();
    echo "✓ Connexion réussie!\n\n";

    // Test basic queries
    echo "=== Tests des tables ===\n";

    // Check arbres table
    $result = $pdo->query('SELECT COUNT(*) as count FROM arbres');
    $row = $result->fetch();
    echo "✓ Table 'arbres' OK - " . $row['count'] . " arbres trouvés\n";

    // Check users table
    $result = $pdo->query('SELECT COUNT(*) as count FROM users');
    $row = $result->fetch();
    echo "✓ Table 'users' OK - " . $row['count'] . " utilisateurs trouvés\n";

    // Test insert permissions
    echo "\n=== Tests des permissions ===\n";
    $pdo->exec('START TRANSACTION');
    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)');
        $stmt->execute(['test_connection_' . time(), password_hash('test', PASSWORD_DEFAULT), 'user']);
        echo "✓ Insertion possible\n";

        $pdo->exec('DELETE FROM users WHERE username LIKE "test_connection_%"');
        $pdo->exec('ROLLBACK');
        echo "✓ Suppression possible (rollback effectué)\n";
    } catch (Exception $e) {
        $pdo->exec('ROLLBACK');
        echo "✗ Erreur lors du test d'insertion: " . $e->getMessage() . "\n";
    }

    echo "\n=== ✓ Tous les tests réussis! ===\n\n";
} catch (RuntimeException $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "\n\n";
    exit(1);
}
