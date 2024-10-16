<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Étudiants - Gestion d'Assiduité</title>
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
                        <a class="nav-link active" href="#">Liste des Étudiants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste_cours.php">Liste des Cours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Emploi du temps général</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal : Liste des Étudiants -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Liste des Étudiants</h2>

        <!-- Liste des Étudiants -->
        <ul class="list-group">
            <?php
            // Connexion à la base de données
            $conn = new mysqli('localhost', 'root', '', 'gestion_cours');
            if ($conn->connect_error) {
                die("Erreur de connexion : " . $conn->connect_error);
            }

            // Requête pour récupérer les étudiants avec leur groupe
            $query = "SELECT etudiants.id, etudiants.nom AS etudiant_nom, groupes.nom AS groupe_nom 
                      FROM etudiants 
                      JOIN groupes ON etudiants.id_groupe = groupes.id";
            $result = $conn->query($query);

            // Vérifier s'il y a des étudiants à afficher
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                        {$row['etudiant_nom']} - <strong>{$row['groupe_nom']}</strong>
                        <div class='action-btns'>
                            <form action='supprimer_etudiant.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                            </form>
                            <form action='affecter_groupe.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <select name='id_groupe' class='form-select form-select-sm' required>
                                    <option value=''>Affecter à un groupe</option>";
                    // Requête pour récupérer les groupes
                    $groupes = $conn->query("SELECT * FROM groupes");
                    while ($groupe = $groupes->fetch_assoc()) {
                        echo "<option value='{$groupe['id']}'>{$groupe['nom']}</option>";
                    }
                    echo "        </select>
                                <button type='submit' class='btn btn-warning btn-sm'>Affecter</button>
                            </form>
                        </div>
                    </li>";
                }
            } else {
                echo "<li class='list-group-item'>Aucun étudiant trouvé.</li>";
            }

            // Fermeture de la connexion à la base de données
            $conn->close();
            ?>
        </ul>

        <div class="text-center mt-4">
            <a href="addEtudiant.php" class="btn btn-success">Ajouter un Étudiant</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>