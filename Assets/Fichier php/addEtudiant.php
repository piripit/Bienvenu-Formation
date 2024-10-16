<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $id_groupe = $_POST['id_groupe'];
    $id_cours = $_POST['id_cours']; // Tableau des cours choisis

    // Insertion de l'étudiant
    $sql = "INSERT INTO etudiants (nom, id_groupe) VALUES ('$nom', '$id_groupe')";
    if ($conn->query($sql) === TRUE) {
        $id_etudiant = $conn->insert_id;

        // Affecter l'étudiant aux cours
        foreach ($id_cours as $cours) {
            $conn->query("INSERT INTO etudiant_cours (id_etudiant, id_cours) VALUES ('$id_etudiant', '$cours')");
        }

        echo "L'étudiant a été ajouté avec succès.";
    } else {
        echo "Erreur : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Ajouter un Étudiant</h2>

        <form action="" method="POST">
            <!-- Nom de l'étudiant -->
            <div class="mb-3">
                <label for="nom" class="form-label">Nom de l'Étudiant</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>

            <!-- Sélection du groupe -->
            <div class="mb-3">
                <label for="id_groupe" class="form-label">Groupe</label>
                <select class="form-select" id="id_groupe" name="id_groupe" required>
                    <?php
                    // Récupérer les groupes
                    $groupes = $conn->query("SELECT * FROM groupes");
                    while ($groupe = $groupes->fetch_assoc()) {
                        echo "<option value='{$groupe['id']}'>{$groupe['nom']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Sélection des cours -->
            <div class="mb-3">
                <label for="id_cours" class="form-label">Cours</label>
                <select class="form-select" id="id_cours" name="id_cours[]" multiple required>
                    <?php
                    // Récupérer les cours
                    $cours = $conn->query("SELECT * FROM cours");
                    while ($cour = $cours->fetch_assoc()) {
                        echo "<option value='{$cour['id']}'>{$cour['nom']}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>

</html>