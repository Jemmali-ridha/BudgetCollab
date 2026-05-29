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
    icone           VARCHAR(50)  DEFAULT NULL,       -- nom d'icône (ex: 'home', 'car')
    couleur         VARCHAR(7)   DEFAULT '#607D8B',  -- code hex
    id_createur     INT UNSIGNED DEFAULT NULL,       -- NULL = catégorie système
    est_systeme     TINYINT(1)   NOT NULL DEFAULT 0, -- 1 = catégorie par défaut
    date_creation   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cat_createur FOREIGN KEY (id_createur) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
);

INSERT INTO categories (nom_categorie, icone, couleur, est_systeme) VALUES
  ('Alimentation',  'shopping-cart', '#4CAF50', 1),
  ('Transport',     'car',           '#2196F3', 1),
  ('Logement',      'home',          '#9C27B0', 1),
  ('Santé',         'heart',         '#F44336', 1),
  ('Loisirs',       'film',          '#FF9800', 1),
  ('Études',        'book',          '#00BCD4', 1),
  ('Vêtements',     'tag',           '#E91E63', 1),
  ('Épargne',       'piggy-bank',    '#8BC34A', 1),
  ('Autres',        'more-horizontal','#607D8B', 1);

CREATE TABLE budgets (
    id_budget INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    budget_name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,

    budget_type ENUM('individual', 'shared') NOT NULL DEFAULT 'individual',

    start_date DATE NOT NULL,
    end_date DATE NOT NULL,

    total_limit DECIMAL(12,3) DEFAULT NULL,  -- NULL = no global limit

    alert_threshold ENUM('50%', '75%', '90%', '100%') DEFAULT '75%',

    created_by INT UNSIGNED NOT NULL,

    status ENUM('active', 'archived', 'closed') NOT NULL DEFAULT 'active',

    CONSTRAINT fk_budget_creator
        FOREIGN KEY (created_by)
        REFERENCES utilisateurs(id_utilisateur)
);