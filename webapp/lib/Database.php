<?php

declare(strict_types=1);

final class Database
{
    private ?PDO $pdo = null;

    public function pdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $attempts = [];

        if (DB_TYPE === 'mysql') {
            // MySQL connection attempts
            $attempts = [
                [
                    'dsn' => sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME),
                    'user' => DB_USER,
                    'pass' => DB_PASSWORD,
                ],
                [
                    'dsn' => sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME),
                    'user' => DB_USER,
                    'pass' => '',
                ],
            ];
        } else {
            // PostgreSQL connection attempts (legacy)
            $attempts = [
                [
                    'dsn' => sprintf('pgsql:host=%s;port=%s;dbname=%s', DB_HOST, DB_PORT, DB_NAME),
                    'user' => DB_USER,
                    'pass' => DB_PASSWORD,
                ],
                [
                    'dsn' => sprintf('pgsql:host=%s;port=%s;dbname=%s', DB_HOST, DB_PORT, DB_NAME),
                    'user' => DB_USER,
                    'pass' => '',
                ],
                [
                    'dsn' => sprintf('pgsql:host=/var/run/postgresql;dbname=%s', DB_NAME),
                    'user' => DB_USER,
                    'pass' => DB_PASSWORD,
                ],
                [
                    'dsn' => sprintf('pgsql:host=/var/run/postgresql;dbname=%s', DB_NAME),
                    'user' => DB_USER,
                    'pass' => '',
                ],
            ];
        }

        $seen = [];
        $errors = [];

        foreach ($attempts as $attempt) {
            $key = $attempt['dsn'] . '|' . $attempt['user'] . '|' . (string) $attempt['pass'];
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            try {
                $this->pdo = new PDO($attempt['dsn'], $attempt['user'], (string) $attempt['pass'], $options);
                return $this->pdo;
            } catch (PDOException $e) {
                $errors[] = $e->getMessage();
            }
        }

        $dbTypeLabel = DB_TYPE === 'mysql' ? 'MySQL' : 'PostgreSQL';
        $lastError = $errors !== [] ? $errors[count($errors) - 1] : "Erreur inconnue de connexion $dbTypeLabel";
        throw new RuntimeException(
            "Connexion $dbTypeLabel impossible. Vérifiez les variables d'environnement DB_HOST/DB_PORT/DB_NAME/DB_USER/DB_PASSWORD. Dernière erreur: " . $lastError
        );

        return $this->pdo;
    }
}
