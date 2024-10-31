<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$modalMessage = '';
$modalType = ''; // 'success' ou 'error' pour indiquer le type de modal

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $conn->real_escape_string($_POST['nom']);
    $id_groupe = $conn->real_escape_string($_POST['id_groupe']);
    $id_cours = $_POST['id_cours']; // Tableau des cours choisis

    // Préparation de la requête pour insérer un étudiant
    $stmt = $conn->prepare("INSERT INTO etudiants (nom, id_groupe) VALUES (?, ?)");
    $stmt->bind_param("si", $nom, $id_groupe);

    if ($stmt->execute()) {
        $id_etudiant = $stmt->insert_id;

        // Affecter l'étudiant aux cours
        if (!empty($id_cours)) {
            $stmt_cours = $conn->prepare("INSERT INTO etudiant_cours (id_etudiant, id_cours) VALUES (?, ?)");
            foreach ($id_cours as $cours) {
                $stmt_cours->bind_param("ii", $id_etudiant, $cours);
                $stmt_cours->execute();
            }
        }

        // Message de succès pour la modal
        $modalMessage = "L'étudiant a été ajouté avec succès.";
        $modalType = 'success';
    } else {
        // Message d'erreur pour la modal
        $modalMessage = "Erreur lors de l'ajout de l'étudiant : " . $conn->error;
        $modalType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Étudiant - Gestion d'Assiduité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            background-color: #0056b3;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .active {
            font-weight: bold;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Gestion d'assiduité</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="ajouter_etudiant.php">Ajouter Étudiant</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste_etudiants.php">Liste Étudiants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="administration.php">Administration</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">Ajouter un Étudiant</h2>

        <!-- Formulaire d'ajout d'étudiant -->
        <form action="" method="POST">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom de l'Étudiant</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>

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

            <div class="mb-3">
                <label for="id_cours" class="form-label">Cours</label>
                <div>
                    <?php
                    $cours = $conn->query("SELECT * FROM cours");
                    while ($cour = $cours->fetch_assoc()) {
                        echo "<div class='form-check'>
                    <input class='form-check-input' type='checkbox' name='id_cours[]' value='{$cour['id']}' id='cours{$cour['id']}'>
                    <label class='form-check-label' for='cours{$cour['id']}'>{$cour['nom']}</label>
                  </div>";
                    }
                    ?>
                </div>
            </div>


            <button type="submit" class="btn btn-primary w-100">Ajouter</button>
        </form>
    </div>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">
                        <?php echo ($modalType === 'success') ? 'Succès' : 'Erreur'; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $modalMessage; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script pour afficher automatiquement la modal si une action a eu lieu -->
    <?php if ($modalMessage): ?>
        <script>
            var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            resultModal.show();
        </script>
    <?php endif; ?>
</body>

</html>