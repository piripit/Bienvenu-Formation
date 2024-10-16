<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']); // Assurez-vous que l'ID est un entier

    // Requête pour supprimer le cours
    $sql = "DELETE FROM cours WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Cours supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression du cours : " . $conn->error;
    }
}

// Redirigez vers la page de liste des cours après la suppression
header('Location: liste_cours.php');
exit();
$conn->close();
