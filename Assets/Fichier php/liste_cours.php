<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Cours - Gestion d'Assiduité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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

<body class="bg-light">

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
                        <a class="nav-link" href="administration.php">Administration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste_etudiants.php">Liste des Étudiants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Liste des Cours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Emploi du temps général</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal : Liste des Cours -->
    <div class="container mt-5">
        <h2 class="text-center">Liste des Cours</h2>

        <!-- Liste des Cours -->
        <ul class="list-group">
            <?php
            // Connexion à la base de données
            $conn = new mysqli('localhost', 'root', '', 'gestion_cours');
            if ($conn->connect_error) {
                die("Erreur de connexion : " . $conn->connect_error);
            }

            // Requête pour récupérer les cours
            $cours = $conn->query("SELECT * FROM cours");

            // Vérifier s'il y a des cours à afficher
            if ($cours->num_rows > 0) {
                // Affichage de chaque cours avec un bouton de suppression
                while ($course = $cours->fetch_assoc()) {
                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>{$course['nom']}
                    <form action='supprimer_cours.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='id' value='{$course['id']}'>
                        <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                    </form>
                    </li>";
                }
            } else {
                echo "<li class='list-group-item'>Aucun cours n'a été ajouté pour le moment.</li>";
            }

            // Fermeture de la connexion à la base de données
            $conn->close();
            ?>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>