<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Simulation de l'identifiant de cours du professeur
$id_cours = 1;

// Clôturer l'appel si le professeur le demande
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['close_attendance'])) {
    $conn->query("UPDATE appel SET cloture = 1 WHERE id_cours = $id_cours");
}

// Récupérer les étudiants présents du cours
$sql = "
    SELECT e.id AS etudiant_id, e.nom AS etudiant_nom, a.present, a.signe
    FROM etudiants e
    LEFT JOIN appel a ON e.id = a.id_etudiant AND a.id_cours = $id_cours
    WHERE a.present = 1
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion d'assiduité - Professeur</title>
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
        </div>
    </nav>

    <!-- Section des présences -->
    <div class="container mt-5">
        <h2 class="text-center">Liste des Étudiants - Appel en cours</h2>
        <form action="" method="POST">
            <table class="table table-bordered mt-4">
                <thead class="table-primary">
                    <tr>
                        <th>Nom de l'étudiant</th>
                        <th>Présence</th>
                        <th>Signature</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['etudiant_nom']}</td>";
                            echo "<td>" . ($row['present'] ? 'Présent' : 'Absent') . "</td>";
                            echo "<td>" . ($row['signe'] ? 'Signé' : 'Non signé') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Aucun étudiant présent</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- Bouton de clôture de l'appel -->
            <button type="submit" name="close_attendance" class="btn btn-danger">Clôturer l'appel</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>