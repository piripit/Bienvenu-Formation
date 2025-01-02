<?php
$conn = new mysqli('localhost', 'root', 'momo22', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prof_id = $_POST['prof_id'];

    // Supprimer les associations dans les autres tables
    $conn->query("DELETE FROM professeur_groupes WHERE id_professeur = $prof_id");
    $conn->query("DELETE FROM professeur_cours WHERE id_professeur = $prof_id");

    // Supprimer ensuite le professeur
    if ($conn->query("DELETE FROM professeurs WHERE id = $prof_id")) {
        echo "Professeur supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression du professeur : " . $conn->error;
    }
}

$conn->close();
