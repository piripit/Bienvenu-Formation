<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si les données ont été soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_cours = $_POST['nom_cours'];

    // Requête pour ajouter le cours
    $query = "INSERT INTO cours (nom) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $nom_cours);

    if ($stmt->execute()) {
        echo "Le cours a été ajouté avec succès.";
    } else {
        echo "Erreur lors de l'ajout du cours : " . $stmt->error;
    }

    $stmt->close();
}

// Fermeture de la connexion à la base de données
$conn->close();
