<?php
session_start(); // Démarrage de la session pour les notifications

class StudentManager
{
    private $pdo;

    public function __construct($dbconn)
    {
        $this->pdo = $dbconn;
    }

    public function getStudent($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGroups()
    {
        $stmt = $this->pdo->query("SELECT id, nom FROM groupes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCourses()
    {
        $stmt = $this->pdo->query("SELECT id, nom FROM cours");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentCourses($id)
    {
        $stmt = $this->pdo->prepare("SELECT id_cours FROM etudiant_cours WHERE id_etudiant = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function updateStudent($id, $name, $groupId, $courseIds)
    {
        try {
            // Mettre à jour le nom et le groupe de l'étudiant
            $stmt = $this->pdo->prepare("UPDATE etudiants SET nom = ?, id_groupe = ? WHERE id = ?");
            $stmt->execute([$name, $groupId, $id]);

            // Supprimer les cours existants de l'étudiant
            $stmt = $this->pdo->prepare("DELETE FROM etudiant_cours WHERE id_etudiant = ?");
            $stmt->execute([$id]);

            // Insérer les nouveaux cours
            if (!empty($courseIds)) {
                $stmt = $this->pdo->prepare("INSERT INTO etudiant_cours (id_etudiant, id_cours) VALUES (?, ?)");
                foreach ($courseIds as $courseId) {
                    $stmt->execute([$id, $courseId]);
                }
            }

            // Ajouter un message de confirmation
            $_SESSION['message'] = "Étudiant mis à jour avec succès !";
        } catch (PDOException $e) {
            die("Erreur lors de la mise à jour de l'étudiant : " . $e->getMessage());
        }
    }
}

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_cours;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Gestion de la mise à jour de l'étudiant
$manager = new StudentManager($pdo);

if (isset($_GET['id'])) {
    $studentId = $_GET['id'];
    $student = $manager->getStudent($studentId);

    if (!$student) {
        die("Étudiant non trouvé !");
    }

    $groups = $manager->getGroups();
    $courses = $manager->getCourses();
    $studentCourses = $manager->getStudentCourses($studentId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['nom'];
        $groupId = $_POST['groupe'];
        $selectedCourses = isset($_POST['cours']) ? $_POST['cours'] : [];

        $manager->updateStudent($studentId, $name, $groupId, $selectedCourses);

        // Redirection pour afficher le message sans re-soumettre le formulaire
        header("Location: modif_Etudiant.php?id=$studentId");
        exit();
    }
} else {
    die("ID de l'étudiant non spécifié !");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f8fb;
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

        h2 {
            margin-top: 20px;
            margin-bottom: 20px;
            color: #0056b3;
            text-align: center;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .alert {
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-primary:hover {
            background-color: #004494;
            border-color: #004494;
        }
    </style>
</head>

<body>
    <!-- Menu de navigation -->
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
                        <a class="nav-link" href="emplois_du_temps.php">Emploi du Temps</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Modifier les informations de l'étudiant</h2>

        <!-- Message de confirmation -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Supprimer le message après affichage
                ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom :</label>
                    <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($student['nom']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="groupe" class="form-label">Groupe :</label>
                    <select id="groupe" name="groupe" class="form-control" required>
                        <option value="">Sélectionner un groupe</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo $group['id']; ?>" <?php echo ($group['id'] == $student['id_groupe']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($group['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cours :</label>
                    <?php foreach ($courses as $course): ?>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="cours[]" value="<?php echo $course['id']; ?>"
                                <?php echo (in_array($course['id'], $studentCourses)) ? 'checked' : ''; ?>>
                            <label class="form-check-label"><?php echo htmlspecialchars($course['nom']); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="liste_etudiants.php" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>