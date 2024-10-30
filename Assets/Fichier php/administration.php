<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion d'Assiduité</title>
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
                        <a class="nav-link active" aria-current="page" href="administration.php">Administration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste_etudiants.php">Liste des Étudiants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste_cours.php">Liste des Cours</a> <!-- Lien vers la page de cours -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste_professeurs.php">Liste des Professeurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Emploi du temps général</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal : Administration -->
    <div class="container mt-5">
        <h2 class="text-center">Administration</h2>

        <!-- Affichage des statistiques -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Nombre d'Étudiants</h5>
                        <p class="card-text">
                            <?php
                            // Connexion à la base de données
                            $conn = new mysqli('localhost', 'root', '', 'gestion_cours');
                            if ($conn->connect_error) {
                                die("Erreur de connexion : " . $conn->connect_error);
                            }
                            $result = $conn->query("SELECT COUNT(*) AS total FROM etudiants");
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Nombre de Professeurs</h5>
                        <p class="card-text">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) AS total FROM professeurs");
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Nombre de Cours</h5>
                        <p class="card-text">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) AS total FROM cours");
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire d'ajout de cours -->
        <h3 class="mt-5">Ajouter un Cours</h3>
        <form action="ajouter_cours.php" method="POST">
            <div class="mb-3">
                <label for="nom_cours" class="form-label">Nom du Cours</label>
                <input type="text" class="form-control" id="nom_cours" name="nom_cours" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>

        <!-- Fermeture de la connexion à la base de données -->
        <?php $conn->close(); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>