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

        $lastError = $errors !== [] ? $errors[count($errors) - 1] : 'Erreur inconnue de connexion PostgreSQL';
        throw new RuntimeException(
            'Connexion PostgreSQL impossible. Vérifiez PGHOST/PGPORT/PGDATABASE/PGUSER/PGPASSWORD. Dernière erreur: ' . $lastError
        );

        return $this->pdo;
    }
}
