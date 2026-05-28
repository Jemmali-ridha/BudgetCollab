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