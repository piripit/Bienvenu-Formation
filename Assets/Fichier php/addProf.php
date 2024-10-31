<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$modalMessage = '';
$modalType = '';

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $courses = $_POST['courses'] ?? []; // Cours sélectionnés (tableau)
    $groups = $_POST['groups'] ?? []; // Groupes sélectionnés (tableau)

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertion du professeur
    $stmt = $conn->prepare("INSERT INTO professeurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        $prof_id = $stmt->insert_id;
       
        // Assignation du professeur aux cours sélectionnés
        if (!empty($courses)) {
            $stmt_course = $conn->prepare("INSERT INTO professeur_cours (id_professeur, id_cours) VALUES (?, ?)");
            foreach ($courses as $course) {
                $stmt_course->bind_param("ii", $prof_id, $course);
                $stmt_course->execute();
            }
        }

        // Assignation du professeur aux groupes sélectionnés
        if (!empty($groups)) {
            $stmt_group = $conn->prepare("INSERT INTO professeur_groupes (id_professeur, id_groupe) VALUES (?, ?)");
            foreach ($groups as $group) {
                $stmt_group->bind_param("ii", $prof_id, $group);
                $stmt_group->execute();
            }
        }

        // Message de succès et envoi d'un email
        $modalMessage = "Le professeur a été ajouté avec succès.";
        $modalType = 'success';

        $subject = "Bienvenue dans le système de gestion d'assiduité";
        $message = "Bonjour $name,\n\nVoici vos informations de connexion:\nEmail: $email\nMot de passe: $password\n\nVeuillez changer votre mot de passe à votre première connexion.";
        mail($email, $subject, $message);
    } else {
        // Message d'erreur pour la modal
        $modalMessage = "Erreur lors de l'ajout du professeur : " . $conn->error;
        $modalType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Professeur - Gestion d'Assiduité</title>
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
                        <a class="nav-link active" href="addprof.php">Ajouter Professeur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste_professeurs.php">Liste Professeurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="administration.php">Administration</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">Ajouter un Professeur</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nom du Professeur</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cours</label>
                <div>
                    <?php
                    $cours = $conn->query("SELECT id, nom FROM cours");
                    while ($course = $cours->fetch_assoc()) {
                        echo "<div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='courses[]' value='{$course['id']}' id='course{$course['id']}'>
                                <label class='form-check-label' for='course{$course['id']}'>{$course['nom']}</label>
                              </div>";
                    }
                    ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Groupes</label>
                <div>
                    <?php
                    $groupes = $conn->query("SELECT id, nom FROM groupes");
                    while ($group = $groupes->fetch_assoc()) {
                        echo "<div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='groups[]' value='{$group['id']}' id='group{$group['id']}'>
                                <label class='form-check-label' for='group{$group['id']}'>{$group['nom']}</label>
                              </div>";
                    }
                    ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ajouter Professeur</button>
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