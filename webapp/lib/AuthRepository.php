<?php

declare(strict_types=1);

final class AuthRepository
{
    public function __construct(private readonly PDO $pdo) {}

    public function countUsers(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function verify(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        if ($user === null) {
            return null;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }
        return $user;
    }

    public function create(string $username, string $email, string $password, string $role = 'user'): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :hash, :role)'
        );
        return $stmt->execute([
            'username' => $username,
            'email'    => $email ?: null,
            'hash'     => password_hash($password, PASSWORD_BCRYPT),
            'role'     => $role,
        ]);
    }
}
