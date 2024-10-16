<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $cours_ids = $_POST['cours_ids'];

    // Ajouter le professeur à la table professeurs
    $stmt = $conn->prepare("INSERT INTO professeurs (email, mot_de_passe) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $mot_de_passe);
    $stmt->execute();

    // Récupérer l'ID du professeur nouvellement ajouté
    $professeur_id = $stmt->insert_id;

    // Ajouter les relations professeur-cours
    if ($cours_ids) {
        foreach ($cours_ids as $cours_id) {
            $stmt = $conn->prepare("INSERT INTO professeur_cours (id_professeur, id_cours) VALUES (?, ?)");
            $stmt->bind_param("ii", $professeur_id, $cours_id);
            $stmt->execute();
        }
    }

    // Fermer la déclaration et la connexion
    $stmt->close();
    $conn->close();

    // Rediriger ou afficher un message de succès
    header("Location: administration.php");
    exit();
}
