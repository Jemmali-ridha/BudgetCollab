<?php

require_once __DIR__ . '/../config/database.php';

class Category
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        $category = $stmt->fetch();
        return $category ?: null;
    }

    public function create(string $name, ?string $description = null): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO categories (name, description)
            VALUES (?, ?)
        ");

        return $stmt->execute([$name, $description]);
    }

    public function update(int $id, string $name, ?string $description = null): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE categories
            SET name = ?, description = ?
            WHERE id = ?
        ");

        return $stmt->execute([$name, $description, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}