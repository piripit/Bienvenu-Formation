<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Professeurs - Gestion d'Assiduité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            /* Fond léger */
        }

        h2 {
            margin-bottom: 20px;
        }

        table {
            background-color: white;
            /* Fond blanc pour la table */
            border-radius: 5px;
            overflow: hidden;
            /* Pour arrondir les coins de la table */
        }

        thead {
            background-color: #0056b3;
            /* Couleur de fond de l'en-tête */
            color: white;
            /* Couleur du texte de l'en-tête */
        }

        th,
        td {
            vertical-align: middle;
            /* Alignement vertical */
        }

        /* Styles de la barre de navigation */
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
                        <a class="nav-link" href="administration.php">Administration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="liste_professeurs.php">Liste des Professeurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Emploi du temps général</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Liste des Professeurs</h2>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Cours</th>
                    <th>Nombre d'Étudiants</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = new mysqli('localhost', 'root', '', 'gestion_cours');
                if ($conn->connect_error) {
                    die("Erreur de connexion : " . $conn->connect_error);
                }

                $result = $conn->query("SELECT p.id AS prof_id, p.email, c.nom AS nom_cours, COUNT(ec.id_etudiant) AS nombre_etudiants
                                        FROM professeurs p
                                        JOIN professeur_cours pc ON p.id = pc.id_professeur
                                        JOIN cours c ON pc.id_cours = c.id
                                        LEFT JOIN etudiant_cours ec ON c.id = ec.id_cours
                                        GROUP BY p.id, c.id");

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['prof_id']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['nom_cours']}</td>
                            <td>{$row['nombre_etudiants']}</td>
                          </tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
        <div class="text-center">
            <a href="administration.php" class="btn btn-primary">Retour à l'administration</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>