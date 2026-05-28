<?php

require_once __DIR__ . '/../config/database.php';

class User
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function creer(
        string $nom,
        string $prenom,
        string $email,
        string $mdpHash,
        int $idRole = 2,
        ?string $avatar = null
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, id_role, avatar)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$nom, $prenom, $email, $mdpHash, $idRole, $avatar]);
        return (int) $this->pdo->lastInsertId();
    }

    public function emailExiste(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_utilisateur FROM utilisateurs WHERE email = ?'
        );
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    public function trouverParEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.*, r.nom_role 
             FROM utilisateurs u
             LEFT JOIN roles r ON u.id_role = r.id_role
             WHERE u.email = ? 
             LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function trouverParId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.*, r.nom_role 
             FROM utilisateurs u
             LEFT JOIN roles r ON u.id_role = r.id_role
             WHERE u.id_utilisateur = ? 
             LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function mettreAJour(
        int $idUtilisateur,
        ?string $nom = null,
        ?string $prenom = null,
        ?string $email = null,
        ?string $mdpHash = null,
        ?int $idRole = null,
        ?string $avatar = null
    ): bool {
        $fields = [];
        $params = [];
        
        if ($nom !== null) {
            $fields[] = 'nom = ?';
            $params[] = $nom;
        }
        if ($prenom !== null) {
            $fields[] = 'prenom = ?';
            $params[] = $prenom;
        }
        if ($email !== null) {
            $fields[] = 'email = ?';
            $params[] = $email;
        }
        if ($mdpHash !== null) {
            $fields[] = 'mot_de_passe = ?';
            $params[] = $mdpHash;
        }
        if ($idRole !== null) {
            $fields[] = 'id_role = ?';
            $params[] = $idRole;
        }
        if ($avatar !== null) {
            $fields[] = 'avatar = ?';
            $params[] = $avatar;
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $idUtilisateur;
        $sql = 'UPDATE utilisateurs SET ' . implode(', ', $fields) . ' WHERE id_utilisateur = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function supprimer(int $idUtilisateur): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM utilisateurs WHERE id_utilisateur = ?');
        return $stmt->execute([$idUtilisateur]);
    }

    public function getRole(int $idUtilisateur): string|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.nom_role 
             FROM utilisateurs u
             JOIN roles r ON u.id_role = r.id_role
             WHERE u.id_utilisateur = ?'
        );
        $stmt->execute([$idUtilisateur]);
        $result = $stmt->fetch();
        return $result ? $result['nom_role'] : false;
    }

    public function estAdmin(int $idUtilisateur): bool
    {
        $role = $this->getRole($idUtilisateur);
        return $role === 'admin';
    }
}