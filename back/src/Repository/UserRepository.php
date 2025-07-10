<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\User;
use Soosuuke\IaPlatform\Config\Database;
use DateTimeImmutable;
use ReflectionClass;

class UserRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToUser($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        return $data ? $this->mapToUser($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM user');
        $users = [];

        while ($row = $stmt->fetch()) {
            $users[] = $this->mapToUser($row);
        }

        return $users;
    }

    public function save(User $user): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO user (email, password, role, country, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $user->getEmail(),
            $user->getPassword(),
            $user->getRole(),
            $user->getCountry(),
            $user->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToUser(array $data): User
    {
        $user = new User($data['email'], $data['password'], $data['role'], $data['country']);

        // set ID manuellement car pas dans le constructeur
        $ref = new ReflectionClass(User::class);
        $prop = $ref->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($user, (int) $data['id']);

        // set createdAt
        $createdProp = $ref->getProperty('createdAt');
        $createdProp->setAccessible(true);
        $createdProp->setValue($user, new DateTimeImmutable($data['created_at']));

        return $user;
    }
}
