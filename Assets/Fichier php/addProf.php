<?php
$conn = new mysqli('localhost', 'root', '', 'gestion_cours'); // Remplacez par vos données

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); // Hachage du mot de passe

    $stmt = $conn->prepare("INSERT INTO professeurs (email, mot_de_passe) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $mot_de_passe);

    if ($stmt->execute()) {
        echo "Professeur ajouté avec succès.";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
header("Location: admin.php");
exit();
