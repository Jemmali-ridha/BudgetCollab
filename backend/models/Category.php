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

    public function getCustomCategories(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM categories
            WHERE id_createur = ?
            ORDER BY id_categorie DESC
        ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSpendingByCategory(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.id_categorie, c.nom_categorie, c.couleur,
                COALESCE(SUM(t.montant), 0) AS total,
                COUNT(t.id_transaction) AS tx_count
            FROM categories c
            LEFT JOIN transactions t ON t.id_categorie = c.id_categorie
                AND t.id_utilisateur = ?
                AND t.type_transaction = "depense"
            GROUP BY c.id_categorie
            ORDER BY total DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMostActiveThisWeek(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.nom_categorie, COUNT(t.id_transaction) AS tx_count
            FROM transactions t
            JOIN categories c ON t.id_categorie = c.id_categorie
            WHERE t.id_utilisateur = ?
            AND t.date_transaction >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY c.id_categorie
            ORDER BY tx_count DESC
            LIMIT 1
        ');
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
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