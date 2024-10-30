<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si une demande de suppression a été faite
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $conn->real_escape_string($_POST['delete_id']);

    // Supprimer l'étudiant de la base de données
    $conn->query("DELETE FROM etudiants WHERE id = $delete_id");
    $conn->query("DELETE FROM etudiant_cours WHERE id_etudiant = $delete_id");
}

// Requête SQL pour récupérer les étudiants, leurs groupes et leurs cours
$sql = "
        SELECT e.id AS etudiant_id, e.nom AS etudiant_nom, g.nom AS groupe_nom, c.nom AS cours_nom
    FROM etudiants e
    LEFT JOIN groupes g ON e.id_groupe = g.id
    LEFT JOIN etudiant_cours ec ON e.id = ec.id_etudiant
    LEFT JOIN cours c ON ec.id_cours = c.id
    ORDER BY g.nom, e.nom";
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
        }

        h2 {
            margin-bottom: 20px;
        }

        table {
            background-color: white;
            border-radius: 5px;
            overflow: hidden;
        }

        thead {
            background-color: #0056b3;
            color: white;
        }

        th,
        td {
            vertical-align: middle;
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-center">Liste des Étudiants</h2>
            <a href="addEtudiant.php" class="btn btn-primary">Ajouter un Étudiant</a>
        </div>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Nom de l'Étudiant</th>
                    <th>Groupe</th>
                    <th>Cours Suivis</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_groupe = null;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Si on change de groupe, afficher un nouvel en-tête de groupe
                        if ($row['groupe_nom'] !== $current_groupe) {
                            $current_groupe = $row['groupe_nom'];
                            echo "<tr><th colspan='4' class='bg-secondary text-white'>Groupe : $current_groupe</th></tr>";
                        }

                        // Afficher les informations de l'étudiant
                        echo "<tr>";
                        echo "<td>{$row['etudiant_nom']}</td>";
                        echo "<td>{$row['groupe_nom']}</td>";
                        echo "<td>{$row['cours_nom']}</td>";
                        echo "<td>
                                <form action='' method='POST' style='display: inline;'>
                                    <input type='hidden' name='delete_id' value='{$row['etudiant_id']}'>
                                    <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet étudiant ?\");'>Supprimer</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Aucun étudiant trouvé</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>