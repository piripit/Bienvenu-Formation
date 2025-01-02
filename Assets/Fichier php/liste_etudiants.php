<?php
class VerifyInfoStudents
{
    private $pdo;

    public function __construct($dbconn)
    {
        $this->pdo = $dbconn; // Connexion PDO
    }

    public function deleteStudent($studentId)
    {
        try {
            // Supprimer les cours associés à l'étudiant
            $stmt1 = $this->pdo->prepare("DELETE FROM etudiant_cours WHERE id_etudiant = :id_etudiant");
            $stmt1->bindParam(':id_etudiant', $studentId, PDO::PARAM_INT);
            $stmt1->execute();

            // Supprimer l'étudiant
            $stmt2 = $this->pdo->prepare("DELETE FROM etudiants WHERE id = :id");
            $stmt2->bindParam(':id', $studentId, PDO::PARAM_INT);
            $stmt2->execute();
        } catch (PDOException $e) {
            die("Erreur lors de la suppression de l'étudiant : " . $e->getMessage());
        }
    }

    public function getStudents()
    {
        try {
            $sql = "
                SELECT e.id AS etudiant_id, e.nom AS etudiant_nom, g.nom AS groupe_nom,
                       GROUP_CONCAT(c.nom SEPARATOR ', ') AS cours_nom
                FROM etudiants e
                LEFT JOIN groupes g ON e.id_groupe = g.id
                LEFT JOIN etudiant_cours ec ON e.id = ec.id_etudiant
                LEFT JOIN cours c ON ec.id_cours = c.id
                GROUP BY e.id
                ORDER BY g.nom, e.nom";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur lors de la récupération des étudiants : " . $e->getMessage());
        }
    }
}

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_cours;charset=utf8", 'root', 'momo22');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Utilisation de la classe
$studentManager = new VerifyInfoStudents($pdo);

// Vérifier si une suppression est demandée
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $studentManager->deleteStudent($_POST['delete_id']);
}

// Récupérer la liste des étudiants
$students = $studentManager->getStudents();
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

        .group-header {
            background-color: #6c757d;
            color: white;
            font-weight: bold;
        }

        ul.course-list {
            padding-left: 20px;
            margin: 0;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Gestion d'assiduité</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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

                if (!empty($students)) {
                    foreach ($students as $row) {
                        // Si on change de groupe, afficher un nouvel en-tête de groupe
                        if ($row['groupe_nom'] !== $current_groupe) {
                            $current_groupe = $row['groupe_nom'];
                            echo "<tr class='group-header'><td colspan='4'>Groupe : $current_groupe</td></tr>";
                        }

                        // Transformer les cours en une liste à puces
                        $coursList = "";
                        if (!empty($row['cours_nom'])) {
                            $coursArray = explode(", ", $row['cours_nom']);
                            $coursList = "<ul class='course-list'>";
                            foreach ($coursArray as $cours) {
                                $coursList .= "<li>" . htmlspecialchars($cours) . "</li>";
                            }
                            $coursList .= "</ul>";
                        }

                        // Afficher les informations de l'étudiant avec ses cours regroupés
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['etudiant_nom']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['groupe_nom']) . "</td>";
                        echo "<td>$coursList</td>";
                        echo "<td>
                                <form action='' method='POST' style='display: inline;'>
                                    <input type='hidden' name='delete_id' value='" . htmlspecialchars($row['etudiant_id']) . "'>
                                    <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"\u00cates-vous s\u00fbr de vouloir supprimer cet \u00e9tudiant ?\");'>Supprimer</button>
                                </form>
                                 <a href='modif_Etudiant.php?id=" . htmlspecialchars($row['etudiant_id']) . "' class='btn btn-warning btn-sm'>Modifier</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Aucun étudiant trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>