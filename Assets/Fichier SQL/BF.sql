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
    FOREIGN KEY (id_professeur) REFERENCES professeurs(id)
);


-- Table des groupes

CREATE TABLE groupes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    id_cours INT,
    FOREIGN KEY (id_cours) REFERENCES cours(id)
);


-- Table des étudiants

CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    id_groupe INT,
    FOREIGN KEY (id_groupe) REFERENCES groupes(id)
);


-- Table de la présence

CREATE TABLE presence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cours INT,
    id_groupe INT,
    date DATE,
    heure TIME,
    salle VARCHAR(255),
    id_etudiant INT,
    FOREIGN KEY (id_cours) REFERENCES cours(id),
    FOREIGN KEY (id_groupe) REFERENCES groupes(id),
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id)
);