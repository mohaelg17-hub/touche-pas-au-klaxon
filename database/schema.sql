-- =====================================================================
-- Touche pas au klaxon : schema de la base de donnees
-- Compatible MySQL 8 / MariaDB 10.4+
-- =====================================================================

DROP DATABASE IF EXISTS klaxon;
CREATE DATABASE klaxon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE klaxon;

-- ---------------------------------------------------------------------
-- Agences (villes). Gerees uniquement par l'administrateur.
-- ---------------------------------------------------------------------
CREATE TABLE agence (
    id_agence INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom       VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_agence),
    CONSTRAINT uq_agence_nom UNIQUE (nom)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Employes (donnees issues du SI RH, aucune gestion dans l'application).
-- mot_de_passe : hash produit par password_hash() cote PHP.
-- ---------------------------------------------------------------------
CREATE TABLE employe (
    id_employe INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom        VARCHAR(100) NOT NULL,
    prenom     VARCHAR(100) NOT NULL,
    telephone  VARCHAR(20)  NOT NULL,
    email      VARCHAR(150) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role       ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    PRIMARY KEY (id_employe),
    CONSTRAINT uq_employe_email UNIQUE (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Trajets proposes par les employes.
-- Les cles etrangeres sont en RESTRICT (comportement par defaut) :
-- on ne peut pas supprimer une agence encore utilisee par un trajet.
-- ---------------------------------------------------------------------
CREATE TABLE trajet (
    id_trajet         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_agence_depart  INT UNSIGNED NOT NULL,
    id_agence_arrivee INT UNSIGNED NOT NULL,
    date_depart       DATETIME NOT NULL,
    date_arrivee      DATETIME NOT NULL,
    places_total      TINYINT UNSIGNED NOT NULL,
    places_dispo      TINYINT UNSIGNED NOT NULL,
    id_employe        INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_trajet),
    CONSTRAINT fk_trajet_depart  FOREIGN KEY (id_agence_depart)  REFERENCES agence (id_agence),
    CONSTRAINT fk_trajet_arrivee FOREIGN KEY (id_agence_arrivee) REFERENCES agence (id_agence),
    CONSTRAINT fk_trajet_employe FOREIGN KEY (id_employe)        REFERENCES employe (id_employe),
    CONSTRAINT ck_trajet_agences CHECK (id_agence_depart <> id_agence_arrivee),
    CONSTRAINT ck_trajet_dates   CHECK (date_arrivee > date_depart),
    CONSTRAINT ck_trajet_places  CHECK (places_total >= 1 AND places_dispo <= places_total),
    INDEX idx_trajet_depart (date_depart)
) ENGINE=InnoDB;
