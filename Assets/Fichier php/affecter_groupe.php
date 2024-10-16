<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si les données ont été soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $id_groupe = $_POST['id_groupe'];

    // Vérifier que le groupe est sélectionné
    if (!empty($id_groupe)) {
        // Requête pour mettre à jour le groupe de l'étudiant
        $query = "UPDATE etudiants SET id_groupe = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $id_groupe, $id);

        if ($stmt->execute()) {
            echo "L'étudiant a été affecté avec succès au groupe.";
        } else {
            echo "Erreur lors de l'affectation de l'étudiant au groupe : " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Veuillez sélectionner un groupe.";
    }
}

// Fermeture de la connexion à la base de données
$conn->close();
