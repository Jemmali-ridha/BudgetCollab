<?php

class Transaction
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
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
            ':id_utilisateur'  => $data['id_utilisateur'],
            ':id_budget'       => $data['id_budget'],
            ':id_categorie'    => $data['id_categorie'],
            ':type_transaction'=> $data['type_transaction'],
            ':montant'         => $data['montant'],
            ':description'     => $data['description'] ?? null,
            ':date_transaction'=> $data['date_transaction'],
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