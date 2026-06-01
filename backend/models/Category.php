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
        $stmt = $this->pdo->query("SELECT * FROM categories Where est_systeme=1  ORDER BY id_categorie DESC");
        return $stmt->fetchAll();
    }

    public function Custom_category(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM categories
            WHERE id_createur = :user_id
            ORDER BY id_categorie DESC
        ");

        $stmt->execute([
            ':user_id' => $userId
        ]);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        $category = $stmt->fetch();
        return $category ?: null;
    }

    public function create(string $name, ?int $creatorId = null, ?string $icon = null, ?string $color = null,  bool $isSystem = false): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO categories 
                (nom_categorie, id_createur, icone, couleur,  est_systeme)
            VALUES 
                (:name, :creator, :icon, :color , :system)
        ");

        return $stmt->execute([
            ':name'    => $name,
            ':icon'    => $icon,
            ':color'   => $color ?? '#607D8B',
            ':creator' => $creatorId,
            ':system'  => $isSystem ? 1 : 0
        ]);
    }

    public function update(int $id, string $name): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE categories
            SET name = ?,est_systeme = 0
            WHERE id = ?
        ");

        return $stmt->execute([$name, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id_categorie = ?");
        return $stmt->execute([$id]);
    }
}