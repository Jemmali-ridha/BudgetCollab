<?php

require_once __DIR__ . '/../config/database.php';

class Transaction
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function getByUser(int $userId, array $filters = []): array
    {
        $where = ['t.id_utilisateur = :uid'];
        $params = [':uid' => $userId];

        if (!empty($filters['search'])) {
            $where[] = 't.description LIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['category'])) {
            $where[] = 't.id_categorie = :cat';
            $params[':cat'] = (int)$filters['category'];
        }

        if (!empty($filters['type'])) {
            $where[] = 't.type_transaction = :type';
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = 't.date_transaction >= :date_from';
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = 't.date_transaction <= :date_to';
            $params[':date_to'] = $filters['date_to'];
        }

        $whereSQL = implode(' AND ', $where);

        $stmt = $this->pdo->prepare("
            SELECT 
                t.*,
                c.nom_categorie,
                c.icone,
                c.couleur,
                u.nom,
                u.prenom
            FROM transactions t
            JOIN categories c ON c.id_categorie = t.id_categorie
            JOIN utilisateurs u ON u.id_utilisateur = t.id_utilisateur
            WHERE $whereSQL
            ORDER BY t.date_transaction DESC, t.date_creation DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $transactionId, int $userId): array|false
    {
        $stmt = $this->pdo->prepare('
            SELECT t.*, c.nom_categorie, c.icone, c.couleur
            FROM transactions t
            JOIN categories c ON c.id_categorie = t.id_categorie
            WHERE t.id_transaction = ? AND t.id_utilisateur = ?
        ');
        $stmt->execute([$transactionId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO transactions 
                (id_utilisateur, id_budget, id_categorie, type_transaction, montant, description, date_transaction)
            VALUES 
                (:id_utilisateur, :id_budget, :id_categorie, :type_transaction, :montant, :description, :date_transaction)
        ');

        return $stmt->execute([
            ':id_utilisateur'   => $data['id_utilisateur'],
            ':id_budget'        => $data['id_budget'],
            ':id_categorie'     => $data['id_categorie'],
            ':type_transaction' => $data['type_transaction'],
            ':montant'          => $data['montant'],
            ':description'      => $data['description'] ?? null,
            ':date_transaction' => $data['date_transaction'],
        ]);
    }

    public function update(int $transactionId, int $userId, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE transactions SET
                id_budget        = :id_budget,
                id_categorie     = :id_categorie,
                type_transaction = :type_transaction,
                montant          = :montant,
                description      = :description,
                date_transaction = :date_transaction
            WHERE id_transaction = :id_transaction AND id_utilisateur = :id_utilisateur
        ');

        return $stmt->execute([
            ':id_budget'        => $data['id_budget'],
            ':id_categorie'     => $data['id_categorie'],
            ':type_transaction' => $data['type_transaction'],
            ':montant'          => $data['montant'],
            ':description'      => $data['description'] ?? null,
            ':date_transaction' => $data['date_transaction'],
            ':id_transaction'   => $transactionId,
            ':id_utilisateur'   => $userId,
        ]);
    }

    public function delete(int $transactionId, int $userId): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM transactions 
            WHERE id_transaction = ? AND id_utilisateur = ?
        ');
        return $stmt->execute([$transactionId, $userId]);
    }
}