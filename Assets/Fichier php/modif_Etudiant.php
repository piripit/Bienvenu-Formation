<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Charger les informations de l'étudiant si l'ID est passé dans l'URL
if (isset($_GET['id'])) {
    $id_etudiant = $_GET['id'];
    $result = $conn->query("SELECT * FROM etudiants WHERE id = $id_etudiant");
    $etudiant = $result->fetch_assoc();

    if (!$etudiant) {
        die("Étudiant non trouvé !");
    }

    // Récupérer les ID des cours actuellement associés à l'étudiant
    $current_courses_result = $conn->query("SELECT id_cours FROM etudiant_cours WHERE id_etudiant = $id_etudiant");
    $current_courses = [];
    while ($row = $current_courses_result->fetch_assoc()) {
        $current_courses[] = $row['id_cours'];
    }
} else {
    die("ID de l'étudiant non spécifié !");
}

// Charger les groupes disponibles
$groupes = $conn->query("SELECT id, nom FROM groupes");

// Charger les cours disponibles
$cours = $conn->query("SELECT id, nom FROM cours");

// Mettre à jour les informations de l'étudiant
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $groupe_id = $_POST['groupe']; // ID du groupe sélectionné
    $selected_courses = isset($_POST['cours']) ? $_POST['cours'] : []; // IDs des cours sélectionnés

    // Mettre à jour le nom et le groupe de l'étudiant
    $stmt = $conn->prepare("UPDATE etudiants SET nom = ?, id_groupe = ? WHERE id = ?");
    $stmt->bind_param("sii", $nom, $groupe_id, $id_etudiant);
    $stmt->execute();
    $stmt->close();

    // Supprimer les associations actuelles entre l'étudiant et les cours
    $conn->query("DELETE FROM etudiant_cours WHERE id_etudiant = $id_etudiant");

    // Insérer les nouvelles associations pour les cours sélectionnés
    $stmt = $conn->prepare("INSERT INTO etudiant_cours (id_etudiant, id_cours) VALUES (?, ?)");
    foreach ($selected_courses as $course_id) {
        $stmt->bind_param("ii", $id_etudiant, $course_id);
        $stmt->execute();
    }
    $stmt->close();

    // Redirection après mise à jour
    header("Location: liste_etudiants.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier l'étudiant</title>
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
        <h2>Modifier les informations de l'étudiant</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom :</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($etudiant['nom']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="groupe" class="form-label">Groupe :</label>
                <select id="groupe" name="groupe" class="form-control" required>
                    <option value="">Sélectionner un groupe</option>
                    <?php while ($groupe = $groupes->fetch_assoc()): ?>
                        <option value="<?php echo $groupe['id']; ?>" <?php echo ($groupe['id'] == $etudiant['id_groupe']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($groupe['nom']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Cours :</label>
                <div class="form-check">
                    <?php while ($cour = $cours->fetch_assoc()): ?>
                        <input type="checkbox" class="form-check-input" name="cours[]" value="<?php echo $cour['id']; ?>"
                            <?php echo (in_array($cour['id'], $current_courses)) ? 'checked' : ''; ?>>
                        <label class="form-check-label">
                            <?php echo htmlspecialchars($cour['nom']); ?>
                        </label>
                        <br>
                    <?php endwhile; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="liste_etudiants.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>