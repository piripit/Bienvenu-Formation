-- Créer la base de données

CREATE DATABASE gestion_cours;

-- Utiliser la base de données

USE gestion_cours;

-- Table des professeurs

CREATE TABLE professeurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

-- Table des cours

CREATE TABLE cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    id_professeur INT,
    FOREIGN KEY (id_professeur) REFERENCES professeurs (id)
);

-- Table des groupes

CREATE TABLE groupes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    id_cours INT,
    FOREIGN KEY (id_cours) REFERENCES cours (id)
);

-- Table des étudiants

CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    id_groupe INT,
    FOREIGN KEY (id_groupe) REFERENCES groupes (id)
);

-- Table de la présence

CREATE TABLE presence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cours INT,
    id_groupe INT,
    DATE DATE,
    heure TIME,
    salle VARCHAR(255),
    id_etudiant INT,
    FOREIGN KEY (id_cours) REFERENCES cours (id),
    FOREIGN KEY (id_groupe) REFERENCES groupes (id),
    FOREIGN KEY (id_etudiant) REFERENCES etudiants (id)
);

INSERT INTO
    groupes (nom)
VALUES ('BTS 2 SIO Slam'),
    ('BTS 2 SIO SISR 1'),
    ('BTS 2 SIO SISR 2');

INSERT INTO
    cours (nom)
VALUES ('Maths'),
    ('Français'),
    ('Anglais'),
    ('CEJM'),
    ('Support'),
    ('Option 1 Slam'),
    ('Option 2 SISR'),
    ('Cybersécurité');

CREATE TABLE professeur_cours (
    id_professeur INT,
    id_cours INT,
    FOREIGN KEY (id_professeur) REFERENCES professeurs (id) ON DELETE CASCADE,
    FOREIGN KEY (id_cours) REFERENCES cours (id) ON DELETE CASCADE,
    PRIMARY KEY (id_professeur, id_cours)
);

CREATE TABLE etudiant_cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT,
    id_cours INT,
    FOREIGN KEY (id_etudiant) REFERENCES etudiants (id) ON DELETE CASCADE,
    FOREIGN KEY (id_cours) REFERENCES cours (id) ON DELETE CASCADE
);
CREATE TABLE professeur_groupes (
    id_professeur INT,
    id_groupe INT,
    PRIMARY KEY (id_professeur, id_groupe),
    FOREIGN KEY (id_professeur) REFERENCES professeurs(id),
    FOREIGN KEY (id_groupe) REFERENCES groupes(id) -- Assurez-vous que la table groupes existe également
);
ALTER TABLE professeurs ADD COLUMN nom VARCHAR(255);

CREATE TABLE appel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cours INT NOT NULL,
    id_etudiant INT NOT NULL,
    present TINYINT(1) DEFAULT 0,
    signe TINYINT(1) DEFAULT 0,
    cloture TINYINT(1) DEFAULT 0,
    FOREIGN KEY (id_cours) REFERENCES cours(id),  -- Assurez-vous que la table cours existe
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id)  -- Assurez-vous que la table etudiants existe
);
DROP TABLE appel;
DROP TABLE presence;
CREATE TABLE emploi_du_temps_professeur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_professeur INT NOT NULL,
    id_cours INT NOT NULL,
    date_heure DATETIME NOT NULL,
    FOREIGN KEY (id_professeur) REFERENCES professeurs(id),
    FOREIGN KEY (id_cours) REFERENCES cours(id)
);

CREATE TABLE emploi_du_temps_etudiant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_cours INT NOT NULL,
    date_heure DATETIME NOT NULL,
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id),
    FOREIGN KEY (id_cours) REFERENCES cours(id)
);
SELECT id FROM cours WHERE id_professeur = {$professeur['id']} ORDER BY RAND() LIMIT 1 ;
SELECT id FROM cours WHERE id_professeur = :id_professeur ORDER BY RAND() LIMIT 1;
