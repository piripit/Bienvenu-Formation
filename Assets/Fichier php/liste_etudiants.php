<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête SQL pour récupérer les étudiants, leurs groupes et leurs cours
$sql = "
    SELECT e.nom AS etudiant_nom, g.nom AS groupe_nom, c.nom AS cours_nom
    FROM etudiants e
    LEFT JOIN groupes g ON e.id_groupe = g.id
    LEFT JOIN etudiant_cours ec ON e.id = ec.id_etudiant
    LEFT JOIN cours c ON ec.id_cours = c.id
    ORDER BY e.nom, g.nom, c.nom";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Étudiants - Gestion d'Assiduité</title>
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
                        <a class="nav-link active" href="liste_etudiants.php">Liste des Étudiants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Emploi du temps général</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Liste des Étudiants</h2>

        <!-- Tableau des étudiants, leurs groupes et les cours suivis -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Nom de l'Étudiant</th>
                    <th>Groupe</th>
                    <th>Cours Suivis</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_etudiant = null;
                $cours_list = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Si on change d'étudiant, afficher l'ancien avec ses cours
                        if ($row['etudiant_nom'] !== $current_etudiant && $current_etudiant !== null) {
                            echo "<tr>";
                            echo "<td>$current_etudiant</td>";
                            echo "<td>$current_groupe</td>";
                            echo "<td>" . implode(', ', $cours_list) . "</td>";
                            echo "</tr>";
                            // Réinitialiser la liste des cours
                            $cours_list = [];
                        }

                        // Ajouter le cours actuel à la liste des cours de l'étudiant
                        $current_etudiant = $row['etudiant_nom'];
                        $current_groupe = $row['groupe_nom'];
                        if ($row['cours_nom']) {
                            $cours_list[] = $row['cours_nom'];
                        }
                    }

                    // Afficher le dernier étudiant, son groupe et ses cours
                    if (!empty($cours_list)) {
                        echo "<tr>";
                        echo "<td>$current_etudiant</td>";
                        echo "<td>$current_groupe</td>";
                        echo "<td>" . implode(', ', $cours_list) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Aucun étudiant trouvé</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>