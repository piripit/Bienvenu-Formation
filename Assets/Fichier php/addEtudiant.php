<?php
$conn = new mysqli('localhost', 'root', '', 'gestion_cours'); // Remplacez par vos données

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $id_groupe = $_POST['id_groupe'];

    $stmt = $conn->prepare("INSERT INTO etudiants (nom, id_groupe) VALUES (?, ?)");
    $stmt->bind_param("si", $nom, $id_groupe);

    if ($stmt->execute()) {
        echo "Étudiant ajouté avec succès.";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
header("Location: administration.php");
exit();
