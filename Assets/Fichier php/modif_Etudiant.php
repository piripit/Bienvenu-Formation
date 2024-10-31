<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours'); 
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si une requête POST a été envoyée
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID de l'étudiant à supprimer
    $id_etudiant = $_POST['id'];

    // Préparer la requête de suppression
    $stmt = $conn->prepare("ALTER TABLE etudiants WHERE id = ?");
    $stmt->bind_param("i", $id_etudiant);

    // Exécuter la requête
    if ($stmt->execute()) {
        echo "L'étudiant a été supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression : " . $stmt->error;
    }

    // Fermer la requête
    $stmt->close();
}

// Fermer la connexion
$conn->close();

// Redirection vers la liste des étudiants après suppression
header("Location: liste_etudiants.php");
exit();
