CREATE DATABASE IF NOT EXISTS budget_collaboratif
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
 
USE budget_collaboratif;

CREATE TABLE roles (
    id_role       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom_role      VARCHAR(50)  NOT NULL UNIQUE,  -- 'admin', 'utilisateur'
    description   VARCHAR(255)
);
 
INSERT INTO roles (nom_role, description) VALUES
  ('admin',        'Administrateur système avec accès complet'),
  ('utilisateur',  'Utilisateur standard gérant ses propres budgets');
 

CREATE TABLE utilisateurs (
    id_utilisateur  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL,
    prenom          VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe    VARCHAR(255) NOT NULL,
    id_role         INT UNSIGNED NOT NULL DEFAULT 2,
    avatar          VARCHAR(255)  DEFAULT NULL,
    date_creation   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_util_role FOREIGN KEY (id_role) REFERENCES roles(id_role)
);

CREATE TABLE categories (
    id_categorie    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom_categorie   VARCHAR(100) NOT NULL,
    icone           VARCHAR(50)  DEFAULT NULL,
    couleur         VARCHAR(7)   DEFAULT '#607D8B',
    id_createur     INT UNSIGNED DEFAULT NULL,   
    est_systeme     TINYINT(1)   NOT NULL DEFAULT 0, 
    date_creation   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cat_createur FOREIGN KEY (id_createur) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
);

INSERT INTO categories (nom_categorie, icone, couleur, est_systeme) VALUES
  ('Food',  'shopping-cart', '#4CAF50', 1),
  ('Transport',     'car',           '#2196F3', 1),
  ('Accommodation',      'home',          '#9C27B0', 1),
  ('Health',         'heart',         '#F44336', 1),
  ('Hobbies',       'film',          '#FF9800', 1),
  ('Studies',        'book',          '#00BCD4', 1),
  ('Clothes',     'tag',           '#E91E63', 1),
  ('Savings',       'piggy-bank',    '#8BC34A', 1),
  ('Others',        'more-horizontal','#607D8B', 1);

CREATE TABLE budgets (
    id_budget INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    budget_name VARCHAR(150) NOT NULL,

    budget_type ENUM('individual', 'shared') NOT NULL DEFAULT 'individual',

    start_date DATE NOT NULL,
    end_date DATE NOT NULL,

    total_limit DECIMAL(12,3) DEFAULT NULL, 

    alert_threshold ENUM('50%', '75%', '90%', '100%') DEFAULT '75%',

    created_by INT UNSIGNED NOT NULL,

    status ENUM('active', 'archived', 'closed') NOT NULL DEFAULT 'active',

    CONSTRAINT fk_budget_creator
        FOREIGN KEY (created_by)
        REFERENCES utilisateurs(id_utilisateur)
);

ALTER TABLE budgets
DROP COLUMN status;

CREATE TABLE transactions (
    id_transaction  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur  INT UNSIGNED NOT NULL,           -- auteur de la transaction
    id_budget       INT UNSIGNED NOT NULL,
    id_categorie    INT UNSIGNED NOT NULL,
    type_transaction ENUM('revenu','depense') NOT NULL,
    montant         DECIMAL(12,3) NOT NULL,
    description     VARCHAR(255)  DEFAULT NULL,
    date_transaction DATE          NOT NULL,
    date_creation   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tr_util   FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur),
    CONSTRAINT fk_tr_budget FOREIGN KEY (id_budget)      REFERENCES budgets(id_budget),
    CONSTRAINT fk_tr_cat    FOREIGN KEY (id_categorie)   REFERENCES categories(id_categorie),
    CONSTRAINT chk_montant  CHECK (montant > 0)
);

CREATE TABLE budget_members (
    id_budget      INT UNSIGNED NOT NULL,
    id_utilisateur INT UNSIGNED NOT NULL,
    joined_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_budget, id_utilisateur),
    FOREIGN KEY (id_budget)      REFERENCES budgets(id_budget),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
);

CREATE TABLE budget_invitations (
    id_invitation  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_budget      INT UNSIGNED NOT NULL,
    invited_by     INT UNSIGNED NOT NULL,
    invited_user   INT UNSIGNED NOT NULL,
    status         ENUM('pending', 'accepted', 'declined') NOT NULL DEFAULT 'pending',
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_inv_budget  FOREIGN KEY (id_budget)    REFERENCES budgets(id_budget),
    CONSTRAINT fk_inv_by      FOREIGN KEY (invited_by)   REFERENCES utilisateurs(id_utilisateur),
    CONSTRAINT fk_inv_user    FOREIGN KEY (invited_user) REFERENCES utilisateurs(id_utilisateur),
    UNIQUE KEY uq_invite (id_budget, invited_user)
);

ALTER TABLE transactions
DROP FOREIGN KEY fk_tr_budget;

ALTER TABLE transactions
ADD CONSTRAINT fk_tr_budget
FOREIGN KEY (id_budget)
REFERENCES budgets(id_budget)
ON DELETE CASCADE;